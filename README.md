Aluna: Emilly Marteninghe Fortes

Usuários Login:
Administrador: tem acesso aos sistemas cadastrados e a paginas especificas.
- Email: admin@teste.com
- Senha: admin123

Usuário teste: tem acesso comum as paginas
- Email: jogador@teste.com
- Senha: password
---------------------------------------------
O que é necessário para funcionar:

Laravel: https://herd.laravel.com/windows

Laragon: https://laragon.org/download

Visual Studio Code: https://code.visualstudio.com/

Abra cada arquivo e finalize a instalação, com isso:

Abra o Laragon e selecione a pasta do projeto.
Aceite as permissões do Laragon e inicie o serviço.
Gere os códigos pelo terminal do Visual Studio Code ou pelo terminal do Laragon.

---------------------------------------------
Passo a passo, de esses comandos no terminal do visual
1. composer install

2. cp .env.example .env

3. php artisan migrate --seed

4. php artisan serve

5. php artisan storage:link

6. Acessar o sistema na pagina: http://localhost:8000/

Se der erro, coloque no terminal: php artisan key:generate

