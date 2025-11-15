Aluna: Emilly Marteninghe Fortes

Usuários Login:
Administrador: tem acesso aos sistemas cadastrados e a paginas especificas.
- Email: admin@teste.com
- Senha: password

Usuário teste: tem acesso comu as paginas
- Email: jogador@teste.com
- Senha: password

1. Instalar as dependências do projeto
composer install

2. copia e cola o .env.example e renomeia para .env
cp .env.example .env

3. Criar as tabelas do banco de dados do projeto
php artisan migrate

Ou criar as tabelas e insere os registros no banco de dados do projeto
php artisan migrate --seed

4. Iniciar o sistema
php artisan serve

5. Acessar o sistema
http://localhost:8000/

