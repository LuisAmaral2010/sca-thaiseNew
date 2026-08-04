Projeto criado durante o curso de Laravel 12.

## Requisitos

* PHP 8.2 ou superior - Conferir a versão: php -v
* MySQL 8.0 ou superior - Conferir a versão: mysql --version
* Composer - Conferir a instalação: composer --version
* Node.js 22 ou superior - Conferir a versão: node -v
* GIT - Conferir se está instalado o GIT: git -v

## Como rodar o projeto baixado

- Duplicar o arquivo ".env.example" e renomear para ".env".
- Alterar as credenciais do banco de dados.
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=celke
DB_USERNAME=root
DB_PASSWORD=MaluPedro1!
```

Instalar as dependências do PHP.
```
composer install
```

Instalar as dependências do Node.js.
```
npm install
```

Gerar a chave no arquivo .env.
```
php artisan key:generate
```

Executar as migrations para criar as tabelas e as colunas.
```
php artisan migrate
```

Executar seed com php artisan para cadastrar registros de testes.
```
php artisan db:seed
```

Iniciar o projeto criado com Laravel.
```
php artisan serve
```

Executar as bibliotecas Node.js.
```
npm run dev
```

Acessar a página criada com Laravel.
```
http://127.0.0.1:8000
```

## Sequência para criar o projeto

Criar o projeto com Laravel
```
composer create-project laravel/laravel .
```

Iniciar o projeto criado com Laravel.
```
php artisan serve
```

Instalar as dependências do Node.js.
```
npm install
```

Executar as bibliotecas Node.js.
```
npm run dev
```

Acessar o conteúdo padrão do Laravel
```
http://127.0.0.1:8000
```

Criar Controller com php artisan.
```
php artisan make:controller NomeController
```
```
php artisan make:controller CoursesController
```

Criar View com php artisan.
```
php artisan make:view diretorio.nome-view
```
```
php artisan make:view courses.index
```

Criar migration com php artisan.
```
php artisan make:migration create_nome_table
```
```
php artisan make:migration create_courses_table
```

Executar as migrations para criar a base de dados e as tabelas.
```
php artisan migrate
```

Criar seed com php artisan para cadastrar registros de testes.
```
php artisan make:seeder NomeSeeder
```
```
php artisan make:seeder UserSeeder
```

Executar seed com php artisan para cadastrar registros de testes.
```
php artisan db:seed
```

Desfazer todas as migrations e executá-las novamente.
```
php artisan migrate:fresh
```

Desfazer todas as migrations, executá-las novamente e rodar as seeds.
```
php artisan migrate:fresh --seed
```

Criar componente
```
php artisan make:component nome --view
```
```
php artisan make:component alert --view
```

Criar o arquivo de Request com validações para o formulário.
```
php artisan make:request NomeRequest
```
```
php artisan make:request UserRequest
```

Traduzir para português [Módulo pt-BR](https://github.com/lucascudo/laravel-pt-BR-localization)

Instalar o pacote de auditoria do Laravel.
```
composer require owen-it/laravel-auditing
```

Publicar a configuração e as migration para auditoria.
```
php artisan vendor:publish --provider "OwenIt\Auditing\AuditingServiceProvider" --tag="config"
```
```
php artisan vendor:publish --provider "OwenIt\Auditing\AuditingServiceProvider" --tag="migrations"
```

Limpar cache de configuração.
```
php artisan config:clear
```

## Como enviar e baixar os arquivos do GitHub

- Criar o repositório **"curso-laravel-12"** no GitHub.
- Criar o branch **"develop"** no repositório.

Baixar os arquivos do Git.
```
git clone -b <branch_name> <repository_url> .
```

- Colocar o código fonte do projeto no diretório que está trabalhando.

Alterar o Usuário Globalmente (para todos os repositórios).
```
git config --global user.name "SeuNomeDeUsuario"
git config --global user.email "seuemail@exemplo.com"
```

Verificar em qual está branch.
```
git branch 
```

Baixar as atualizações do GitHub.
```
git pull
```

Adicionar todos os arquivos modificados no staging area - área de preparação.
```
git add .
```

commit representa um conjunto de alterações e um ponto específico da história do seu projeto, registra apenas as alterações adicionadas ao índice de preparação.
O comando -m permite que insira a mensagem de commit diretamente na linha de comando.
```
git commit -m "Base projeto"
```

Enviar os commits locais, para um repositório remoto.
```
git push <remote> <branch>
git push origin develop
```

## Autor

Este projeto foi desenvolvido por [Cesar Szpak](https://github.com/cesarszpak) e está hospedado no repositório da organização [Celke](https://github.com/celkecursos).

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE.txt) para mais detalhes.