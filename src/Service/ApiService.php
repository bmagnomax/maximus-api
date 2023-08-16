<?php

namespace MaximusApi\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
class ApiService
{
    protected $model;
    protected $responseFormat;

    public function __construct($responseFormat = null)
    {
        $this->responseFormat = $responseFormat ?? config('maximus.response_format', 'json');
    }

    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model::all();
    }

    public function find($id)
    {
        return $this->model::findOrFail($id);
    }

    public function create(Request $request)
    {
        $model = $this->model::create($request->all());

        if ($this->responseFormat === 'json') {
            return response()->json($model, 201);
        } else {
            return $model;
        }
    }

    public function update(Request $request, $id)
    {
        $model = $this->model::findOrFail($id);
        $model->update($request->all());

        if ($this->responseFormat === 'json') {
            return response()->json($model, 200);
        } else {
            return $model;
        }
    }

    public function delete($id)
    {
        $model = $this->model::findOrFail($id);
        $model->delete();

        if ($this->responseFormat === 'json') {
            return response()->json(['message' => 'Record deleted successfully'], 200);
        } else {
            return $model;
        }
    }

    public function search(Request $request)
    {
        $query = $this->model->newQuery();

        foreach ($request->all() as $field => $value) {
            // Ignore campos que não existam no modelo
            if (Schema::hasColumn($this->model->getTable(), $field)) {
                // Tratar o valor do campo para evitar SQL injection
                $value = $this->sanitizeValue($value);

                // Realizar a pesquisa usando o operador "LIKE"
                $query->orWhere($field, 'LIKE', "%$value%");
            }
        }

        return $query->get();
    }

    // Método para tratar o valor e evitar SQL injection
    protected function sanitizeValue($value)
    {
        return str_replace(['%', '_'], ['\%', '\_'], $value);
    }

    public function withRelations(Request $request)
    {
        // Obter os relacionamentos da query string do Request
        $relationships = $request->input('relationships');

        // Verificar se o parâmetro está presente
        if ($relationships) {
            // Dividir a string de relacionamentos em um array
            $relations = explode(',', $relationships);

            // Carregar os relacionamentos e retornar os usuários com os relacionamentos
            return $this->model->with($relations)->get();
        } else {
            // Caso o parâmetro não esteja presente, retornar todos os usuários sem relacionamentos
            return $this->model->all();
        }
    }


    public function searchWith(Request $request, $perPage = 10)
{
    $query = $this->model->newQuery();

    // Obter o parâmetro de página da query string do Request
    $page = $request->input('page', 1);

    // Obter os relacionamentos da query string do Request
    $relationships = $request->input('relationships');

    // Verificar se o parâmetro está presente
    if ($relationships) {
        // Dividir a string de relacionamentos em um array
        $relations = explode(',', $relationships);

        // Carregar os relacionamentos na consulta
        $query->with($relations);
    }

    // Obter os campos de pesquisa da query string do Request
    $searchFields = $request->input('searchFields');

    // Verificar se o parâmetro está presente
    if ($searchFields) {
        // Dividir a string de campos de pesquisa em um array
        $fields = explode(',', $searchFields);

        // Aplicar a pesquisa aos campos especificados na query string
        foreach ($fields as $fieldWithValue) {
            // Dividir o campo e o valor usando os delimitadores corretos
            if (strpos($fieldWithValue, ':') !== false) {
                list($field, $value) = explode(':', $fieldWithValue, 2);
                $operator = 'LIKE'; // Usar o operador "LIKE" por padrão
            } elseif (strpos($fieldWithValue, '&') !== false) {
                list($field, $value) = explode('&', $fieldWithValue, 2);
                $operator = '=';
            } elseif (strpos($fieldWithValue, '@') !== false) {
                list($field, $values) = explode('@', $fieldWithValue, 2);
                $value = explode(',', $values);
                $operator = 'IN';
            } else {
                continue; // Ignorar campos inválidos
            }

            // Verificar se o campo existe no modelo
            if (Schema::hasColumn($this->model->getTable(), $field)) {
                // Tratar o valor do campo para evitar SQL injection
                $value = $this->sanitizeValue($value);

                // Aplicar a pesquisa usando o operador apropriado
                switch ($operator) {
                    case 'LIKE':
                        $query->orWhere($field, 'LIKE', "%$value%");
                        break;
                    case '=':
                        $query->where($field, '=', $value);
                        break;
                    case 'IN':
                        $query->whereIn($field, $value);
                        break;
                }
            }
        }
    }

    // Realizar a paginação dos resultados
    $results = $query->paginate($perPage, ['*'], 'page', $page);

    return $results;
    // return $query->get();
}

public function searchWithByRelations(Request $request, $perPage = 10)
    {
        $query = $this->model->newQuery();

        // Processar os parâmetros da query string para realizar a busca
        foreach ($request->query() as $key => $value) {
            // Verificar se o campo é um campo do modelo principal
            if (Schema::hasColumn($this->model->getTable(), $key)) {
                // Realizar a busca diretamente no modelo principal
                $this->applySearchCondition( $query, $key, $value);
            } else {
                // Verificar se o campo é um campo de relacionamento
                list($relationship, $field) = explode('.', $key, 2);
                if ($this->hasRelationship($relationship)) {
                    // Realizar a busca no modelo do relacionamento
                    $this->applySearchCondition( $query, $field, $value, $relationship);
                }
            }
        }

        // Obter o parâmetro de ordenação da query string do Request
        $orderBy = $request->input('orderBy', 'id');
        $sort = $request->input('sort', 'asc');

        // Realizar a ordenação dos resultados
        $query->orderBy($orderBy, $sort);

        // Obter o parâmetro de página da query string do Request
        $page = $request->input('page', 1);

        // Realizar a paginação dos resultados
        $results = $query->paginate($perPage, ['*'], 'page', $page);

        return $results;
    }

    protected function applySearchCondition( $query, $field, $value, $relationship = null)
    {
        // Verificar se o campo pertence a um relacionamento ou ao modelo principal
        if ($relationship) {
            $relatedModel = $query->getModel()->{$relationship}()->getRelated();
            $query->orWhereHas($relationship, function ( $q) use ($relatedModel, $field, $value) {
                $this->applySearchCondition($q, $field, $value);
            });
        } else {
            // Tratar o valor do campo para evitar SQL injection
            $value = $this->sanitizeValue($value);

            // Realizar a busca usando o operador "LIKE"
            $query->orWhere($field, 'LIKE', "%$value%");
        }
    }

    protected function hasRelationship($relationship)
    {
        // Verificar se o modelo possui o relacionamento especificado
        return method_exists($this->model, $relationship);
    }

}
