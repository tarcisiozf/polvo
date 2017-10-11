<!DOCTYPE HTML>
<html ng-app="polvo">
    <head>
        <title>CRUD</title>
        <meta charset="utf-8">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
        <style>

            .jumbotron {
                width: 750px;
                margin: 20px auto;
                box-sizing: border-box;
                padding: 20px 25px;
            }

            .title {
                font-size: 20px;
                color: #333;
                margin-top: 15px; 
            }
            
            .form-control {
                width: 50%;
            }

            .btn {
                margin: 15px 0px;
            }

            .btn-action {
                margin: 0px;
                float: right;
            }
        </style>
    </head>
    <body ng-controller="controller">
        <div class="jumbotron">
            <h3>Produtos</h3>
            <hr>
            <section>
                <div class="title">Nome:</div><br/>
                <input type="text" class="form-control" ng-model="product.name" >
                <div class="title">SKU:</div><br/>
                <input type="text" class="form-control" ng-model="product.sku" >
                <div class="title">Preço:</div><br/>
                <input type="number" step="0.01" class="form-control" ng-model="product.price" >
                <div class="title">Descrição:</div><br/>
                <textarea class="form-control" ng-model="product.description" ></textarea>
                <button class="btn btn-success" ng-click="saveProduct()">{{product_btn}}</button>
            </section>
            <hr>
            <section>
                <table class="table">
                    <tr>
                        <td><b>Nome:</b></td>
                        <td><b>SKU:</b></td>
                        <td><b>Preço:</b></td>
                        <td colspan="3"><b>Descrição:</b></td>
                    </tr>
                    <tr ng-repeat="item in products">
                        <td>{{item.name}}</td>
                        <td>{{item.sku}}</td>
                        <td>{{item.price}}</td>
                        <td>{{item.description}}</td>
                        <td>
                            <button class="btn btn-default btn-action" ng-click="editProduct(item)">Editar</button>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-action" ng-click="deleteProduct(item)">Deletar</button>
                        </td>
                    </tr>
                </table>
            </section>
        </div>
        <div class="jumbotron">
            <h3>Adicionar Pedido</h3>
            <hr>
            <section>
                <div class="title">Valor total: R$ {{valorTotal}}</div><br/>
                <div class="title">Produto:</div><br/>
                <select class="form-control" ng-model="id_product">
                    <option ng-repeat="item in products" value="{{item.id}}">{{item.name}}</option>
                </select>
                <div class="title">Quantidade:</div><br/>
                <input type="number" class="form-control" ng-model="amount" >
                <button class="btn btn-primary" ng-click="addProductToOrder()">ADICIONAR PRODUTO</button>
            </section>
            <section>
                <div class="title">Produtos selecionados:</div><br/>
                <table class="table">
                    <tr>
                        <td><b>Nome:</b></td>
                        <td colspan="2"><b>Quantidade:</b></td>
                    </tr>
                    <tr ng-repeat="(key, item) in order.products">
                        <td>{{item.name}}</td>
                        <td>{{item.amount}}</td>
                        <td>
                            <button class="btn btn-default btn-action" ng-click="removeProductFromOrder(key)">Remover produto</button>
                        </td>
                    </tr>
                </table>
            </section>
            <button class="btn btn-success" ng-click="saveOrder()">{{order_btn}}</button>
        </div>
    </body>
    <script>
        angular.module('polvo', []);
        angular.module('polvo').controller('controller', function($scope, $http) {

            $scope.products = [];
            $scope.product = {};
            $scope.product_btn = "INSERIR";

            $scope.valorTotal = 0.0;
            $scope.order_product = {};
            $scope.order_btn = "INSERIR PEDIDO";
            $scope.order = {
                products: []
            };

            function insertOrder(order) {

                $http({
                    method: 'post',
                    url: '/orders',
                    data: { products: order.products }
                })
                .then(function(response) {
                    $scope.order = { products: [] };
                    $scope.valorTotal = 0;
                }, tratamentoDeErro);

            }

            $scope.saveOrder = function() {

                var order = angular.copy($scope.order);

                if ( order.id ) {
                    updateOrder(order);
                } else {
                    insertOrder(order);
                }

            }

            function atualizarValorTotal() {

                var total = 0;

                for(var item of $scope.order.products) {
                    var product = findProductById(item.id_product);
                    total += item.amount * product.price;
                }

                $scope.valorTotal = total;
            }

            function findProductById(id) {

                for(var prod of $scope.products) {
                    if ( prod.id == id ) {
                        return prod;
                    }
                }

                return null;
            }

            $scope.addProductToOrder = function() {
                
                var product = findProductById($scope.id_product);

                $scope.order.products.push({
                    id_product: angular.copy($scope.id_product),
                    amount: angular.copy($scope.amount),
                    name: product.name
                });

                $scope.id_product = null;
                $scope.amount = null;

                atualizarValorTotal();
            }

            $scope.removeProductFromOrder = function(key) {
                $scope.order.products.splice(key, 1);
                atualizarValorTotal();
            }
            
            // Insere o produto via API
            function insertProduct(product) {

                $http({
                    method: 'post',
                    url: '/products',
                    data: product
                })
                .then(function(response) {
                    $scope.products.push(product);
                    $scope.product = {};
                }, tratamentoDeErro);

            }

            // Atualiza o produto via API
            function updateProduct(product) {

                $http({
                    method: 'put',
                    url: '/products/' + product.id,
                    data: product
                })
                .then(function(response) {
                    $scope.product = {};
                    $scope.product_btn = "INSERIR";
                }, tratamentoDeErro);

            }
            
            // Ação vinda do botão, verifica se é um registro novo ou atualização
            $scope.saveProduct = function() {

                var product = angular.copy($scope.product);

                if ( product.id ) {
                    updateProduct(product);
                } else {
                    insertProduct(product);
                }

            }

            // Confirma e excluir o produto
            $scope.deleteProduct = function(product) {

                if ( ! window.confirm("Tem certeza que deseja excluir o produto "+ product.name +"?") ) {
                    return;
                }

                $http({
                    method: 'delete',
                    url: '/products/' + product.id
                }).then(function(response) {
                    $scope.products.splice($scope.products.indexOf(product), 1);
                }, tratamentoDeErro);

            }

            // Carrega o produto para o formulário
            $scope.editProduct = function(product) {
                $scope.product = product;
                $scope.product_btn = "SALVAR EDIÇÃO";
                window.scroll(0,0);
            }

            // Tratamento genérico para esse cenário
            function tratamentoDeErro(data, status) {
                console.error(data, status);
            }

            // Trás a lista de produtos do back-end
            $http({
                method: 'get',
                url: "/products"
            }).then(function(response) {
                $scope.products = response.data;
            }, tratamentoDeErro);

            // Trás a lista de pedidos do back-end
            $http({
                method: 'get',
                url: "/orders"
            }).then(function(response) {
                console.log(response.data);
                // $scope.products = response.data;
            }, tratamentoDeErro);

        });
    </script>
</html>