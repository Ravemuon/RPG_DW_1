Aluna: Emilly Marteninghe Fortes

Usuários Login:
Administrador: tem acesso aos sistemas cadastrados e a paginas especificas.
- Email: admin@teste.com
- Senha: password

Usuário teste: tem acesso comum as paginas
- Email: jogador@teste.com
- Senha: password


---------------------------------------------
Passo a passo, de esses comandos no terminal do visual
1. composer install

2. cp .env.example .env

3. php artisan migrate --seed

4. php artisan serve

5. Acessar o sistema na pagina: http://localhost:8000/

Se der erro, coloque no terminal: php artisan key:generate

