# Módulo de pagamentos Ifthenpay magento 2

Ler em ![Português](https://github.com/ifthenpay/magento2/raw/assets/assets/img/pt.png) [Português](readme.pt.md), e ![Inglês](https://github.com/ifthenpay/magento2/raw/assets/assets/img/en.png) [Inglês](readme.md)

[1. Introdução](#introdução)

[2. Compatibilidade](#compatibilidade)

[3. Instalação](#instalação)
  * [Instalação usando o composer](#instalação-usando-o-composer)
  * [Instalação manual](#instalação-manual)

[4. Configuração](#configuração)
  * [Chave Backoffice](#chave-backoffice)
  * [Multibanco](#multibanco)
  * [Multibanco com Referências Dinâmicas](#multibanco-com-referências-dinâmicas)
  * [MB WAY](#mb-way)
  * [Cartão de Crédito](#cartão-de-crédito)
  * [Payshop](#payshop)
  * [Cofidis Pay](#cofidis-pay)
  * [Pix](#pix)
  * [Ifthenpay Gateway](#ifthenpay-gateway)


[5. Devoluções](#devoluções)

[6. Multistore](#multistore)

[7. Outros](#outros)
  * [Requerer criação de conta adicional](#requerer-criação-de-conta-adicional)
  * [Reset de Configuração](#reset-de-configuração)
  * [Callback](#callback)
  * [Cronjob](#cronjob)
  * [Logs](#logs)


[8. Experiência do Utilizador Consumidor](#experiência-do-utilizador-consumidor)
  * [Pagar encomenda com Multibanco](#pagar-encomenda-com-multibanco)
  * [Pagar encomenda com Payshop](#pagar-encomenda-com-payshop)
  * [Pagar encomenda com MB WAY](#pagar-encomenda-com-mb-way)
  * [Pagar encomenda com Credit Card](#pagar-encomenda-com-cartão-de-crédito)
  * [Pagar encomenda com Cofidis Pay](#pagar-encomenda-com-cofidis-pay)
  * [Pagar encomenda com Pix](#Pagar-encomenda-com-Pix)
  * [Pagar encomenda com Ifthenpay Gateway](#pagar-encomenda-com-ifthenpay-gateway)




# Introdução
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/payment_methods_banner.png)

**Este é o plugin Ifthenpay para a plataforma de e-commerce Magento 2**

**Multibanco** é um método de pagamento que permite ao consumidor pagar com referência bancária.
Este módulo permite gerar referências de pagamento que o consumidor pode usar para pagar a sua encomenda numa caixa multibanco ou num serviço online de Home Banking. Este plugin usa a Ifthenpay, uma das várias gateways disponíveis em Portugal.

**MB WAY** é a primeira solução inter-bancos que permite a compra e transferência imediata por via de smartphone e tablet.
Este módulo permite gerar um pedido de pagamento ao smartphone do consumidor, e este pode autorizar o pagamento da sua encomenda na aplicação MB WAY. Este plugin usa a Ifthenpay, uma das várias gateways disponíveis em Portugal.

**Payshop** é um método de pagamento que permite ao consumidor pagar com referência payshop.
Este módulo permite gerar uma referência de pagamento que o consumidor pode usar para pagar a sua encomenda num agente Payshop ou CTT. Este plugin usa a Ifthenpay, uma das várias gateways disponíveis em Portugal.

**Cartão de Crédito** Este módulo permite gerar um pagamento por Visa ou Master card, que o consumidor pode usar para pagar a sua encomenda. Este plugin usa a Ifthenpay, uma das várias gateways disponíveis em Portugal.

**Cofidis Pay** é uma solução de pagamento que facilita o pagamento de compras ao dividir o valor até 12 prestações sem juros. Este módulo utiliza uma das várias gateways/serviços disponíveis em Portugal, a IfthenPay.

**Pix** é uma solução de pagamento instantâneo amplamente usada no mercado financeiro brasileiro. Permite realizar compras de forma rápida e segura, utilizando dados como CPF, e-mail e número de telemóvel para efetuar o pagamento.


**É necessário contrato com a Ifthenpay**

Mais informações em [Ifthenpay](https://ifthenpay.com). 

Adesão em [Adesão Ifthenpay](https://www.ifthenpay.com/aderir/).

**Suporte**

Para suporte, por favor crie um ticked para suporte em [Suporte Ifthenpay](https://helpdesk.ifthenpay.com/).









# Compatibilidade

Use a tabela abaixo para verificar a compatibilidade do módulo Ifthenpay com a sua loja online.
|                            | Magento 2.3    | Magento 2.4 [2.4.0 - 2.4.6] |
|----------------------------|----------------|-----------------------------|
| Ifthenpay v1.0.0 - v1.2.13 | Não compatível | Compatível até 2.4.5        |
| Ifthenpay v2.0.0 - v2.3.0  | Não compatível | Compatível                  |








# Instalação

É possível instalar o módulo de duas formas: usando o composer ou colocando os ficheiros manualmente na pasta app/code/.

## Instalação usando o composer

1. Aceda à pasta raiz da sua loja online usando o terminal, isto pode ser feito por ligação SSH ou usando o terminal do seu alojamento web.

2. Execute os seguintes comandos em sequência:


```bash
composer require ifthenpay/magento2
```

```bash
php bin/magento setup:upgrade
```

```bash
php bin/magento setup:di:compile
```

```bash
php bin/magento cache:clean
```

## Instalação manual

1. Faça download da versão mais recente do módulo em [Ifthenpay Github](https://github.com/ifthenpay/magento2/releases).

![download github](https://github.com/ifthenpay/magento2/raw/assets/assets/img/githubDownload.png)
</br>

2. Caso não exista, crie as seguintes pastas na raiz da sua loja online: app/code/Ifthenpay/Payment e coloque os ficheiros do módulo dentro da pasta criada.

![download github](https://github.com/ifthenpay/magento2/raw/assets/assets/img/folderExample.png)
</br>

3. Execute os seguintes comandos em sequência:

```bash
php bin/magento setup:upgrade
```

```bash
php bin/magento setup:di:compile
```
 
```bash
php bin/magento cache:clean
```



# Configuração

Após a instalação do módulo, este estará disponivel nas configurações da sua loja online.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/goConfiguracoes.png)
</br>

Escolha Vendas -> Métodos de Pagamento e, encontrando o módulo Ifthenpay, clique em Configurar.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/goIfthenpay.png)
</br>


## Chave Backoffice
A Chave Backoffice é dada na conclusão do contrato e é constituída para conjuntos de quatro algarismos separados por um traço (-).
Introduza a Chave de Backoffice (1) e clique em salvar (2).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/insertBackofficeKey.png)
</br>

## Multibanco
Clique em Multibanco (1) para expandir as opções de configuração.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/expandMultibanco.png)
</br>


O método de pagamento Multibanco, gera referências por algoritmo e é usado se não desejar atribuir um tempo limite (em dias) para encomendas pagas com Multibanco.
A Entidade e Sub-Entidade são carregadas automáticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Habilitado** - Ao selecionar sim, ativa o método de pagamento, exibindo-o no checkout da sua loja.
2. **Título** - Título que aparece ao consumidor no checkout, no caso de escolher não exibir o ícone.
3. **Exibir Ícone** - Ao selecionar sim, exibe o ícone do método de pagamento no checkout.
4. **Ativar Callback** - Ao selecionar sim, o estado da encomenda será atualizado quando o pagamento for recebido;
5. **Entidade** - Selecionar uma Entidade. Apenas pode selecionar uma das Entidades associadas à Chave Backoffice;
6. **Subentidade** - Selecionar uma Sub-Entidade. Apenas pode selecionar uma das Sub-Entidades associadas à Entidade escolhida anteriormente;
7. **Enviar Email de Fatura** - Ao selecionar sim, o consumidor recebe automáticamente um email com a fatura da encomenda quando o pagamento for recebido;
8. **Valor Mínimo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor superior ao valor inserido;
9. **Valor Máximo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor inferior ao valor inserido;
10. **Restringir Pagamento a Países** - (opcional) Selecionar todos os países ou apenas os países especificos, deixar vazio para permitir todos os países;
11. **Pagamento de países específicos** - (opcional) Apenas exibe este método de pagamento para encomendas com destino de envio dentro dos países selecionados, deixar vazio para permitir todos os países;
12. **Ordenação** - (opcional) Ordena os métodos de pagamento na página de checkout de forma ascendente. Número mais baixo toma o primeiro lugar;

Clicar em salvar para guardar as alterações.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationMultibanco.png)
</br>



## Multibanco com Referências Dinâmicas
O método de pagamento Multibanco com Referências Dinâmicas, gera referências por pedido e é usado se desejar atribuir um tempo limite (em dias) para encomendas pagas com Multibanco.
A Entidade e Chave Multibanco são carregadas automáticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

Seguir os passos da configuração do Multibanco com a seguinte alteração:

1. **Entidade** - Selecionar "Referências Dinâmicas de Multibanco", esta entidade só estará disponível para seleção se tiver efetuado contrato para criação de conta Multibanco com Referências Dinâmicas;
2. **Chave Multibanco** - Selecionar uma Chave Multibanco. Apenas pode selecionar uma das Chaves Multibanco associadas à Entidade escolhida anteriormente;
3. **Validade** - Selecionar o número de dias de validade da referência Multibanco. Ao selecionar 0, a referência Multibanco expira às 23:59 do mesmo dia em que foi gerada. Ao deixar vazio, a referência Multibanco não expira;
4. clicar em salvar para guardar as alterações.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationMultibancoDynamic.png)
</br>


## MB WAY
O método de pagamento MB WAY, usa um número de telemóvel dado pelo consumidor e gera um pedido de pagamento à aplicação MB WAY do smartphone deste, a qual pode aceitar ou recusar.
As Chaves MB WAY  são carregadas automáticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Habilitado** - Ao selecionar sim, ativa o método de pagamento, exibindo-o no checkout da sua loja.
2. **Título** - Título que aparece ao consumidor no checkout, no caso de escolher não exibir o ícone.
3. **Exibir Ícone** - Ao selecionar sim, exibe o ícone do método de pagamento no checkout.
4. **Exibir Contagem** - (opcional) Ao selecionar sim, exibe a contagem decrescente do tempo limite para pagamento na página de sucesso da encomenda. Selecionar não se encontrar conflitos com módulos de one page checkout;
5. **Ativar Callback** - Ao selecionar sim, o estado da encomenda será atualizado quando o pagamento for recebido;
6. **Chave MB WAY** - Selecionar uma Chave. Apenas pode selecionar uma das Chaves associadas à Chave Backoffice;
7. **Enviar Email de Fatura** - Ao selecionar sim, o consumidor recebe automáticamente um email com a fatura da encomenda quando o pagamento for recebido;
8. **Permitir devolução** - Ao selecionar sim, exibe um botão na página de nota de crédito que permite um administrador da loja online devolver o valor pago pelo consumidor;
9. **Valor Mínimo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor superior ao valor inserido;
10. **Valor Máximo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor inferior ao valor inserido;
11. **Restringir Pagamento a Países** - (opcional) Selecionar todos os países ou apenas os países especificos, deixar vazio para permitir todos os países;
12. **Pagamento de países específicos** - (opcional) Apenas exibe este método de pagamento para encomendas com destino de envio dentro dos países selecionados, deixar vazio para permitir todos os países;
13. **Ordenação** - (opcional) Ordena os métodos de pagamento na página de checkout de forma ascendente. Número mais baixo toma o primeiro lugar;

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationMbway.png)
</br>


## Cartão de Crédito
O método de pagamento Cartão de Crédito, permite pagar com cartão de crédito Visa ou Mastercard através da gateway Ifthenpay.
As Chaves Ccard são carregadas automáticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Habilitado** - Ao selecionar sim, ativa o método de pagamento, exibindo-o no checkout da sua loja.
2. **Título** - Título que aparece ao consumidor no checkout, no caso de escolher não exibir o ícone.
3. **Exibir Ícone** - Ao selecionar sim, exibe o ícone do método de pagamento no checkout.
4. **Chave Ccard** - Selecionar uma Chave. Apenas pode selecionar uma das Chaves associadas à Chave Backoffice;
5. **Enviar Email de Fatura** - Ao selecionar sim, o consumidor recebe automáticamente um email com a fatura da encomenda quando o pagamento for recebido;
6. **Permitir devolução** - Ao selecionar sim, exibe um botão na página de nota de crédito que permite um administrador da loja online devolver o valor pago pelo consumidor;
7. **Valor Mínimo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor superior ao valor inserido;
8. **Valor Máximo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor inferior ao valor inserido;
9. **Restringir Pagamento a Países** - (opcional) Selecionar todos os países ou apenas os países especificos, deixar vazio para permitir todos os países;
10. **Pagamento de países específicos** - (opcional) Apenas exibe este método de pagamento para encomendas com destino de envio dentro dos países selecionados, deixar vazio para permitir todos os países;
11. **Ordenação** - (opcional) Ordena os métodos de pagamento na página de checkout de forma ascendente. Número mais baixo toma o primeiro lugar;

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationCCard.png)
</br>


## Payshop

O método de pagamento Payshop, gera uma referência que pode ser paga em qualquer agente payshop ou loja aderente.
As Chaves Payshop  são carregadas automáticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Habilitado** - Ao selecionar sim, ativa o método de pagamento, exibindo-o no checkout da sua loja.
2. **Título** - Título que aparece ao consumidor no checkout, no caso de escolher não exibir o ícone.
3. **Exibir Ícone** - Ao selecionar sim, exibe o ícone do método de pagamento no checkout.
4. **Ativar Callback** - Ao selecionar sim, o estado da encomenda será atualizado quando o pagamento for recebido;
5. **Chave Payshop** - Selecionar uma Chave. Apenas pode selecionar uma das Chaves associadas à Chave Backoffice;
6. **Validade** - Selecionar o número de dias de validade da referência Payshop. De 1 a 99 dias, deixe vazio não pretender que expire;
7. **Enviar Email de Fatura** - Ao selecionar sim, o consumidor recebe automáticamente um email com a fatura da encomenda quando o pagamento for recebido;
8. **Valor Mínimo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor superior ao valor inserido;
9. **Valor Máximo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor inferior ao valor inserido;
10. **Restringir Pagamento a Países** - (opcional) Selecionar todos os países ou apenas os países especificos, deixar vazio para permitir todos os países;
11. **Pagamento de países específicos** - (opcional) Apenas exibe este método de pagamento para encomendas com destino de envio dentro dos países selecionados, deixar vazio para permitir todos os países;
12. **Ordenação** - (opcional) Ordena os métodos de pagamento na página de checkout de forma ascendente. Número mais baixo toma o primeiro lugar;

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationPayshop.png)
</br>


## Cofidis Pay

O método de pagamento Cofidis Pay permite ao consumidor pagar em prestações.
As Chaves Cofidis Pay são carregadas automáticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Habilitado** - Ao selecionar sim, ativa o método de pagamento, exibindo-o no checkout da sua loja.
2. **Título** - Título que aparece ao consumidor no checkout, no caso de escolher não exibir o ícone.
3. **Exibir Ícone** - Ao selecionar sim, exibe o ícone do método de pagamento no checkout.
4. **Ativar Callback** - Ao selecionar sim, o estado da encomenda será atualizado quando o pagamento for recebido;
5. **Chave Cofidis Pay** - Selecionar uma Chave. Apenas pode selecionar uma das Chaves associadas à Chave Backoffice;
8. **Valor Mínimo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor superior ao valor inserido;
6. **Enviar Email de Fatura** - Ao selecionar sim, o consumidor recebe automáticamente um email com a fatura da encomenda quando o pagamento for recebido;
9. **Valor Máximo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor inferior ao valor inserido;
9. **Restringir Pagamento a Países** - (opcional) Selecionar todos os países ou apenas os países especificos, deixar vazio para permitir todos os países;
10. **Pagamento de países específicos** - (opcional) Apenas exibe este método de pagamento para encomendas com destino de envio dentro dos países selecionados, deixar vazio para permitir todos os países;
11. **Ordenação** - (opcional) Ordena os métodos de pagamento na página de checkout de forma ascendente. Número mais baixo toma o primeiro lugar;

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationCofidis.png)
</br>


## Pix

O método de pagamento Pix permite ao consumidor pagar em prestações.
As Chaves Pix são carregadas automáticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Habilitado** - Ao selecionar sim, ativa o método de pagamento, exibindo-o no checkout da sua loja.
2. **Título** - Título que aparece ao consumidor no checkout, no caso de escolher não exibir o ícone.
3. **Exibir Ícone** - Ao selecionar sim, exibe o ícone do método de pagamento no checkout.
4. **Ativar Callback** - Ao selecionar sim, o estado da encomenda será atualizado quando o pagamento for recebido;
5. **Chave Pix** - Selecionar uma Chave. Apenas pode selecionar uma das Chaves associadas à Chave Backoffice;
6. **Enviar Email de Fatura** - Ao selecionar sim, o consumidor recebe automáticamente um email com a fatura da encomenda quando o pagamento for recebido;
7. **Valor Mínimo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor superior ao valor inserido;
8. **Valor Máximo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor inferior ao valor inserido;
9. **Restringir Pagamento a Países** - (opcional) Selecionar todos os países ou apenas os países especificos, deixar vazio para permitir todos os países;
10. **Pagamento de países específicos** - (opcional) Apenas exibe este método de pagamento para encomendas com destino de envio dentro dos países selecionados, deixar vazio para permitir todos os países;
11. **Ordenação** - (opcional) Ordena os métodos de pagamento na página de checkout de forma ascendente. Número mais baixo toma o primeiro lugar;

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationCofidis.png)
</br>


## Ifthenpay Gateway

O método de pagamento Ifthenpay Gateway permite ao comsumidor, após ser redirecionado para página de gateway de pagamento, escolher um dos métodos de pagamento mencionados acima para pagar a sua encomenda.
As Chaves Ifthenpay Gateway são carregadas automaticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Habilitado** - Ao selecionar sim, ativa o método de pagamento, exibindo-o no checkout da sua loja.
2. **Ativar Callback** - Ao selecionar sim, o estado da encomenda será atualizado quando o pagamento for recebido;
3. **Chave Ifthenpay Gateway** - Selecionar uma Chave. Apenas pode selecionar uma das Chaves associadas à Chave Backoffice;
4. **Métodos de Pagamento** - Selecionar uma conta por cada método de pagamento e colocar o visto na checkbox dos métodos que pretende exibir na página de gateway;
5. **Método de Pagamento por Defeito** - Selecionar um método de pagamento que estará selecionado por defeito na página da gateway quando o consumidor aceder a esta;
6. **Validade** - Selecionar o número de dias de validade da referência Payshop. De 1 a 99 dias, deixe vazio se não pretender que expire;
7. **Exibir Ícone** - (opcional) Exibe o logo deste método de pagamento no checkout, escolha uma de três opções:
    - Por defeito: Exibe o logo ifthenpay gateway;
    - Título: Exibe o Título do método de pagamento;
    - Compósito: Exibe uma imagem composta por todos os logos dos métodos de pagamento selecionados;  
8. **Título** - Título que aparece ao consumidor no checkout, no caso de escolher não exibir o ícone.
9. **Texto do Botão de Fechar Gateway** - Texto exíbido no botão de "Voltar para loja" na página da gateway;
10. **Enviar Email de Fatura** - Ao selecionar sim, o consumidor recebe automáticamente um email com a fatura da encomenda quando o pagamento for recebido;
11. **Valor Mínimo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor superior ao valor inserido;
12. **Valor Máximo** - (opcional) Apenas exibe este método de pagamento para encomendas com valor inferior ao valor inserido;
13. **Restringir Pagamento a Países** - (opcional) Selecionar todos os países ou apenas os países especificos, deixar vazio para permitir todos os países;
14. **Pagamento de países específicos** - (opcional) Apenas exibe este método de pagamento para encomendas com destino de envio dentro dos países selecionados, deixar vazio para permitir todos os países;
15. **Ordenação** - (opcional) Ordena os métodos de pagamento na página de checkout de forma ascendente. Número mais baixo toma o primeiro lugar;

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/configurationIfthenpaygateway.png)
</br>


## Devoluções

Os métodos de pagamento MB WAY e Cartão de Crédito permitem devolução do valor total ou parcial pago pelo consumidor através da página de nota de crédito da encomenda.
Para devolver o valor pago pelo consumidor, é necessário que o método de pagamento tenha a opção "Permitir devolução" ativada e que exista uma fatura da encomenda.
Para proceder à devolução do valor pago pelo consumidor, aceda à página de encomendas.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/goOrders.png)
</br>

Aceda aos detalhes da encomenda (1).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/orderDetails.png)
</br>

E clicar em Faturas (1) e em ver detalhes (2).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/goInvoice.png)
</br>

Clicar em Nota de Crédito (1).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/refundCreditMemo.png)
</br>

É possivel editar o valor a devolver (1) e clicar em atualizar (2), ou proceder à devolução do valor total pago pelo consumidor clicando em devolver (3).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/refundPage.png)
</br>

Confirmar o valor a devolver e clicar em OK (1).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/confirmRefund.png)
</br>

Será enviado um email com um token de segurança para o email do utilizador administrador da loja online que iniciou a devolução.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/emailRefund.png)
</br>

Introduzir o token de segurança recebido no email (1) e clicar em OK (2).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/confirmToken.png)
</br>

A valor selecionado será devolvido ao consumidor e o estado da encomenda será atualizado para fechado. 


## Multistore

O módulo Ifthenpay é compatível com o modo multistore do magento 2, permitindo configurar diferentes métodos de pagamento para cada loja.
Esta funcionalidade é aplicada por escopo de website, permitindo configurar diferentes métodos de pagamento para cada website.
Para configurar diferentes métodos de pagamento para cada loja, aceda à página de configurações do módulo e selecione o website pretendido no canto superior esquerdo (1).

IMPORTANTE: Ao implementar multi loja, não deve configurar a Default Config, pois vai sobrepor-se aos websites das sublojas. Apenas é possível configurar diferentes métodos de pagamento para cada website, não é possível configurar diferentes métodos de pagamento para cada Store View.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/multiStoreScope.png)
</br>


# Outros
  
 ## Requerer criação de conta adicional


Se já tem uma conta Ifthenpay, mas não tem contratou um método de pagamento que agora precisa, pode fazer um pedido automático para a Ifthenpay;
Para requerer a criação de uma conta adicional, aceda à página de configurações do módulo e clique em Requerer nova conta para o método de pagamento que pretende contratar.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/requesNewAccount.png)
</br>

No caso de necessitar de uma conta para Multibanco com Referências Dinâmicas, o botão Requerer nova conta estará disponivel dentro da configuração do método de pagamento Multibanco (1).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/requestMultibancoDynamic.png)
</br>


Ao clicar em requerer nova conta exibirá uma caixa de dialogo na qual pode confirmar a ação clicando em OK (1).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/requestAccountConfirm.png)
</br>

Também pode requerer um método para Ifthenpay Gateway, seguindo o mesmo procedimento, clicando num botão de "Requerer" (1).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/request_gateway_paymen_method.png)
</br>

Assim, a equipa da Ifthenpay adicionará o método de pagamento à sua conta, atualizando a lista de métodos de pagamento disponíveis no seu módulo.

IMPORTANTE: Ao pedir uma conta para o método de pagamento por Cartão de Crédito, a equipa da Ifthenpay irá contactá-lo para pedir mais informações sobre a sua loja online e o seu negócio antes de ativar o método de pagamento.



## Reset de Configuração

Se adquiriu uma nova Chave Backoffice e pretende atribuí-la ao seu site, mas já tem uma atualmente atribuída, pode efetuar o reset da configuração do módulo. Na configuração do módulo ifthenpay, clique no botão Limpar Chave Backoffice (1) e confirme a ação clicando em OK.

**Atenção, esta ação irá limpar as atuais configurações do módulo**;

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/clearBackofficeKey.png)
</br>

Após limpar a chave de backoffice, ser-lhe-á mais uma vez pedido para inserir a Chave Backoffice;

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/afterClearBackofficeKey.png)
</br>


## Callback

IMPORTANTE: apenas os métodos de pagamento Multibanco, MB WAY e Payshop permitem ativar o Callback, o cartão de crédito altera o estado da encomenda automáticamente.

O Callback é uma funcionalidade que quando ativa, permite que a sua loja receba a notificação de um pagamento bem-sucedido. Quando ativa, ao receber um pagamento com sucesso de uma encomenda, o servidor da ifthenpay comunica com a sua loja, mudando o estado da encomenda para "Em Processamento". Pode usar os pagamentos da Ifthenpay sem ativar o Callback, mas as suas encomendas não atualizaram o estado automaticamente;

Como mencionado acima em configurações, para ativar o Callback, aceda à página de configurações do módulo e ative a opção Ativar Callback.
Após salvar as configurações, é executado o processo de associação da sua loja e método de pagamento aos servidores da ifthenpay, e será exibido um novo elemento (apenas informativo) que apresenta estado do Callback (1), a chave anti-phishing (2), e a URL do Callback (3).

Após ativar o Callback não necessita de tomar mais nenhuma ação, o Callback está ativo e a funcionar.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/callbackElement.png)
</br>


## Cronjob

Um cronjob é uma tarefa programada que é executada automaticamente em intervalos específicos no sistema. O módulo Ifthenpay disponibiliza um cronjob para verificar o estado dos pagamentos, e cancelar encomendas que não foram pagas dentro do tempo limite configurado. A tabela abaixo mostra o tempo limite para cada método de pagamento, o qual o cronjob verifica e cancela as encomendas que não foram pagas dentro do tempo limite. Este tempo limite pode ser configurado apenas para o método de pagamento Multibanco com Referências Dinâmicas e Payshop.

| Método de Pagamento | Validade do pagamento       |
|---------------------|-----------------------------|
| Multibanco          | não possui                  |
| Multibanco Dinâmico | Configurável de 1 a n dias  |
| MB WAY              | 30 minutos                  |
| Payshop             | Configurável de 1 a 99 dias |
| Cartão de Crédito   | 30 minutos                  |
| Cofidis Pay         | 60 minutos                  |
| Pix                 | 30 minutos                  |

O cronjob de cancelamento de encomenda executa a cada minuto. As opções de configuração do cronjob estão disponíveis na página de configurações de cronjobs do magento no grupo ifthenpay_payment.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cronjobConfiguration.png)
</br>

Para iniciar a execução do cronjob, aceda ao terminal do magento e execute o seguinte comando:

```bash
bin/magento cron:run --group ifthenpay_payment
```

## Logs

Para facilitar a deteção de erros, o módulo Ifthenpay regista os erros ocorridos durante a execução do módulo. Os logs são então guardados num ficheiro de texto na pasta var/log/ do magento. Para aceder aos logs, no root do Magento aceda à pasta var/log/ e abra o ficheiro ifthenpay.log.


# Experiência do Utilizador Consumidor

O seguinte descreve a experiência do utilizador consumidor ao usar os métodos de pagamento da Ifthenpay numa instalação "stock" do Magento, esta pode mudar com a adição de extensões de one-page-checkout.

Na página de checkout, após escolher o método de envio, o consumidor pode escolher o método de pagamento.


## Pagar encomenda com Multibanco

Selecionar o método de pagamento Multibanco (1) e clicar em Fazer Encomenda (2).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/checkoutMultibanco.png)
</br>

Será exibida a página de sucesso da encomenda, com a entidade, referência e o valor a pagar.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouMultibanco.png)
</br>

Se o método de pagamento Multibanco estiver configurado com referências dinâmicas, na página de sucesso da encomenda, será exibido adicionalmente a validade da referência.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouMultibancoDynamic.png)
</br>


## Pagar encomenda com Payshop

Selecionar o método de pagamento Payshop (1) e clicar em Fazer Encomenda (2).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/checkoutPayshop.png)
</br>

Será exibida a página de sucesso da encomenda, com a referência, validade e o valor a pagar.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouPayshop.png)
</br>



## Pagar encomenda com MB WAY

Selecionar o método de pagamento MB WAY (1) preencher o número de telemóvel (2) e clicar em Fazer Encomenda (3).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/checkoutMbway.png)
</br>

No caso da configuração de Exibir Contagem estar ativa, será exibida a contagem decrescente do tempo limite para pagamento na página de sucesso da encomenda.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouMbwayCountDown.png)
</br>

O contador atualizará automáticamente o estado do pagamento no caso de sucesso, rejeiçao (por parte do utilizador da App MB WAY), expiração do tempo limite ou erro.

Em caso de sucesso será exibida a mensagem de sucesso.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouSuccess.png)
</br>

Em caso de rejeição pelo utilizador será exibida a mensagem de rejeitado.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouRejected.png)
</br>

Em caso de expiração do tempo será exibida a mensagem de expirado.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouTimeOut.png)
</br>


Em caso de falha ao comunicar com a App MB WAY ou introdução de um número de telemóvel inválido, será exibida uma mensagem de erro.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouError.png)
</br>


Quando ocorre um erro ou atinge o tempo limite, ou recusa o pagamento na App MB WAY, o consumidor pode tentar novamente clicando em Reenviar notificação MB WAY.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouMbwayResend.png)
</br>


Se na configuração do método de pagamento MB WAY estiver ativa a opção de não exibir o contador, o consumidor receberá uma notificação na App MB WAY, mas não será exibido o contador nem o botão de reenviar notificação na página de sucesso de encomenda.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouMbwayNoCountDown.png)
</br>


## Pagar encomenda com cartão de crédito

Selecionar o método de pagamento Cartão de Crédito (1) e clicar em Fazer Encomenda (2).

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/checkoutCcard.png)
</br>

Preencher os dados do cartão de crédito número do cartão (1), data de validade (2), código de segurança (3), Nome no Cartão (4), e clicar em Pagar (5).

É possível voltar (6), cancelando o pagamento.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/ccardGateway.png)
</br>

Após o pagamento ser processado, será exibida a página de sucesso da encomenda.

![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouCcard.png)
</br>


## Pagar encomenda com Cofidis Pay

Selecionar o método de pagamento Cofidis Pay (1) e clicar em Fazer Encomenda (2).


![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/checkoutCofidis.png)
</br>


* Entre ou, se não tiver conta faça o registo com Cofidis Pay:
1. Clique "Avançar" para registar em Cofidis Pay;
2. Ou se tiver uma conta Cofidis Pay, preencha as suas credencias de acesso e clique entrar;
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cofidis_payment_1.png)
</br>

* Número de prestações, faturação e dados pessoais:
1. Selecione o número de prestações que deseja;
2. Verifique o sumário do plano de pagamento;
3. Preencha os seus dados pessoais e de faturação;
4. Clique em "Avançar" para continuar;
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cofidis_payment_2.png)
</br>

* Termos e condições:
1. Selecione "Li e autorizo" para concordar com os termos e condições;
2. Clique em "Avançar"
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cofidis_payment_3.png)
</br>

* Formalização do acordo:
1. Clique em "Enviar código";
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cofidis_payment_4.png)
</br>

* Código de autenticação da formalização do acordo:
1. Preencha o com o código que recebeu no telemóvel;
2. Clique em "Confirmar código";
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cofidis_payment_5.png)
</br>

* Resumo e Pagamento:
1. Preencha com os detalhes do seu cartão de crédito(número, data de expiração e CW), e clique em "Validar";
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cofidis_payment_6.png)
</br>

* Sucesso e voltar à loja:
1. Clique no icone para voltar à loja;
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cofidis_payment_7.png)
</br>

* Após o qual será redirecionado de volta para a loja;
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/cofidis_payment_return.png)
</br>


## Pagar encomenda com Pix

Selecionar o método de pagamento Pix (1) preencher o nome, CPF e Email (2) (campos relacionados com morada são opcionais) e clicar em Fazer Encomenda (3).
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/checkoutPix.png)
</br>

* Proceder com o pagamento de duas formas:
1. Ler o código QR com o smartphone;
2. Copiar o código Pix e pagar com online banking;
**Nota Importante:** De modo a ser redirecionado de volta para a loja após o pagamento, esta página deve permanecer aberta. Se fechada, o consumidor ainda pode proceder ao pagamento desde que já tenha lido o código Pix, apenas não será redirecionado de volta para a loja.
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/pix_payment_1.png)
</br>

Após o pagamento ser processado, será exibida a página de sucesso da encomenda.
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/thankYouPix.png)
</br>


## Pagar encomenda com Ifthenpay Gateway

Selecionar o método de pagamento Ifthenpay Gateway (1) e clicar em Fazer Encomenda (2).


![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/checkoutIfthenpaygateway.png)
</br>

Selecionar um dos métodos de pagamento disponíveis na página da gateway (1).
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/ifthenpaygateway_payment_1.png)
</br>

No caso do método de pagamento Multibanco, a entidade, referência e valor serão exibidos.
Aqui o consumidor pode fazer uma das duas ações:
 - em caso de método de pagamento offline, guardar os dados de pagamento, clicar em fechar a gateway no botão (2) e pagar mais tarde;
 - pagar no momento e clicar no botão de confirmar pagamento (3) para verificar o pagamento; 
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/ifthenpaygateway_payment_2.png)
</br>

Se o consumidor não pagou no momento e não guardou os dados de pagamento, ainda pode aceder mais tarde a estes através do link de acesso à gateway encontrado no histórico de encomenda na conta de utilizador ou email de confirmação de encomenda. 
![img](https://github.com/ifthenpay/magento2/raw/assets/assets/img/ifthenpaygateway_payment_3.png)
</br>

Chegou ao final do manual do módulo Ifthenpay para Magento 2. Parabéns!
