# Maximus API 

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

A API Maximus utiliza a classe `ApiService` para gerenciar operações CRUD e pesquisas avançadas.

### Endpoints Disponíveis

Abaixo estão listados os endpoints disponíveis na API Maximus:

#### Recurso: Usuários (`/api/users`)

-   `GET /api/users`: Retorna todos os usuários cadastrados.
    
-   `GET /api/users/{id}`: Retorna um usuário específico com base no ID.
    
-   `POST /api/users`: Cria um novo usuário.
    
-   `PUT /api/users/{id}`: Atualiza os detalhes de um usuário existente com base no ID.
    
-   `DELETE /api/users/{id}`: Exclui um usuário com base no ID.
    
-   `GET /api/users/search`: Realiza uma pesquisa avançada nos usuários com filtros específicos.
    
-   `GET /api/users/with`: Retorna todos os usuários com seus relacionamentos especificados.
    
-   `GET /api/users/searchwith`: Realiza uma pesquisa avançada nos usuários, incluindo relacionamentos, com opções de paginação e filtros.
    
-   `GET /api/users/searchwithbyrelations`: Realiza uma pesquisa avançada nos usuários, com suporte a campos dos relacionamentos, paginação e ordenação.
    

### Exemplos de Uso

#### Obtendo Todos os Usuários

**Requisição:** `GET /api/users`

**Resposta:**

    [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
        // outros campos
      },
      // outros usuários
    ]

#### Criando um Novo Usuário

**Requisição:** `POST /api/users`

    {
      "name": "Jane Smith",
      "email": "jane@example.com"
      // outros campos
    }

**Resposta:**

    {
      "id": 2,
      "name": "Jane Smith",
      "email": "jane@example.com"
      // outros campos
    }


#### Pesquisa Avançada com Relacionamentos

**Requisição:** `GET /api/users/searchwith?relationships=posts,comments`

**Resposta:**

    [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "posts": [
          {
            "id": 1,
            "title": "Post Title",
            "content": "Post content"
            // outros campos do post
          }
        ],
        "comments": [
          {
            "id": 1,
            "text": "Comment text"
            // outros campos do comentário
          }
        ]
        // outros campos do usuário
      },
      // outros usuários com relacionamentos
    ]


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


#### Pesquisa Avançada com Relacionamentos e Paginação

**Requisição:** 

    GET /api/users/searchwith?relationships=posts,comments&page=2`

**Resposta:**

    {
      "current_page": 2,
      "data": [
        {
          "id": 11,
          "name": "User 11",
          // outros campos do usuário
          "posts": [
            {
              "id": 21,
              "title": "Post Title",
              // outros campos do post
            }
          ],
          "comments": []
        },
        // outros usuários com relacionamentos
      ],
      "first_page_url": "/api/users/searchwith?relationships=posts,comments&page=1",
      "from": 11,
      "last_page": 5,
      "last_page_url": "/api/users/searchwith?relationships=posts,comments&page=5",
      "next_page_url": "/api/users/searchwith?relationships=posts,comments&page=3",
      "path": "/api/users/searchwith",
      "per_page": 10,
      "prev_page_url": "/api/users/searchwith?relationships=posts,comments&page=1",
      "to": 20,
      "total": 50
    }
    

**Considerações Finais**
    Esta documentação cobre os principais endpoints disponíveis no pacote Maximus Api. Você pode ajustar os parâmetros de consulta para atender às suas necessidades de pesquisa, filtragem e ordenação. Lembre-se de ajustar as configurações em config/maximus.php conforme necessário.

Fique à vontade para explorar mais funcionalidades da API e adaptá-la conforme suas necessidades. Para quaisquer dúvidas ou problemas, não hesite em entrar em contato com nossa equipe de suporte.


