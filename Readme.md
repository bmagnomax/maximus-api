# API Maximus

Bem-vindo ao pacote de API Maximus. Aqui você encontrará informações detalhadas sobre a configuração, os endpoints disponíveis e como usá-los.

## Configuração

Antes de usar a API, você precisará configurar o arquivo `config/maximus.php` com os modelos que deseja expor e outras configurações. Abaixo está um exemplo de configuração:

```php
return [
    'models' => [
        'user' => App\Models\User::class,
        'post' => App\Models\Post::class,
        'comment' => App\Models\Comment::class,
        // Adicione outros modelos aqui
    ],
    'response_format' => 'json', // Opções: 'json' ou 'object'
    'path_models' => 'App\Models\\'
];
```

# Endpoints Disponíveis
## Pesquisa com Relacionamentos e Paginação
Descrição: Retorna uma lista de recursos com suporte para pesquisa avançada, filtragem, ordenação por campos de relacionamentos e paginação.

**Requisição:**

     GET /api/{model}/searchwith

**Parâmetros de Consulta:**

**relationships (opcional)**: Lista de relacionamentos separados por vírgula para carregar junto com os recursos.
**searchFields (opcional)**: Campos e valores para realizar a busca. Use formatos como campo:valor, campo&valor ou campo@valor1,valor2 para diferentes operadores.
**orderBy (opcional)**: Campo para ordenação. Padrão é 'id'.
**sort (opcional)**: Direção da ordenação. Padrão é 'asc'.
**page (opcional)**: Número da página para paginação. Padrão é 1.

**Exemplo de Requisição:**


    GET /api/users/searchwith?relationships=posts,comments&searchFields=name:John,age&orderBy=name&sort=asc&page=2


**Exemplo de Resposta:**

    {   "current_page": 2,   "data": [
        {
          // Recurso com relacionamentos
        },
        // Outros recursos   
        ],
        // Informações de paginação 
    }

**Considerações Finais**
Esta documentação cobre os principais endpoints disponíveis na API do sistema Maximus. Você pode ajustar os parâmetros de consulta para atender às suas necessidades de pesquisa, filtragem e ordenação. Lembre-se de ajustar as configurações em config/maximus.php conforme necessário.

Fique à vontade para explorar mais funcionalidades da API e adaptá-la conforme suas necessidades. Para quaisquer dúvidas ou problemas, não hesite em entrar em contato com nossa equipe de suporte.

