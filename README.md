# Sistema de Clientes

Este é um sistema simples para gerenciamento de clientes, desenvolvido em PHP com integração ao banco de dados MySQL.

## Funcionalidades

- Cadastro de clientes com nome, e-mail e data de nascimento.
- Edição de informações de clientes.
- Exclusão de clientes individuais ou de todos os clientes.
- Busca de clientes por nome ou e-mail.
- Interface moderna utilizando Tailwind CSS e Font Awesome.

## Requisitos

- PHP 7.4 ou superior.
- Servidor MySQL.
- Servidor web (como Apache ou Nginx).
- Composer (opcional, para gerenciar dependências).

## Configuração

1. Clone este repositório ou copie os arquivos para o diretório do seu servidor web.
2. Configure o banco de dados:
   - Crie um banco de dados chamado `sistemas_clientes`.
   - Execute o seguinte script SQL para criar a tabela `clientes`:

     ```sql
     CREATE TABLE clientes (
         id INT AUTO_INCREMENT PRIMARY KEY,
         nome VARCHAR(255) NOT NULL,
         email VARCHAR(255) NOT NULL,
         data_nascimento DATE NULL,
         data_cadastro DATETIME NOT NULL
     );
     ```

3. Atualize as credenciais do banco de dados nos arquivos `index.php` e `config.php`:
   - `$host`, `$dbname`, `$username`, `$password`.

4. Inicie o servidor web e acesse o sistema no navegador.

## Uso

- **Cadastro de Clientes**: Preencha o formulário no lado esquerdo e clique em "Cadastrar Cliente".
- **Edição de Clientes**: Clique no ícone de edição ao lado do cliente desejado, atualize as informações e salve.
- **Exclusão de Clientes**: Clique no ícone de lixeira para excluir um cliente ou use a opção "Limpar todos" para excluir todos os registros.
- **Busca de Clientes**: Use a barra de busca para localizar clientes pelo nome ou e-mail.

## Estrutura do Projeto

- `index.php`: Arquivo principal com a interface e lógica do sistema.
- `config.php`: Arquivo para conexão com o banco de dados e API para obter dados de clientes.
- `README.md`: Documentação do projeto.

## Tecnologias Utilizadas

- **PHP**: Linguagem de programação para o backend.
- **MySQL**: Banco de dados relacional.
- **Tailwind CSS**: Framework CSS para estilização.
- **Font Awesome**: Ícones para a interface.

## Contribuição

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou enviar pull requests.

## Licença

Este projeto está licenciado sob a licença MIT. Consulte o arquivo `LICENSE` para mais detalhes.
