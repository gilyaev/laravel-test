<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Products Lists</div>

                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>
                                    <a href="#" style="display:block" @click.prevent="sort('name')">
                                        Name
                                        <span :class="['glyphicon', 'pull-right', sorting.name.direction === '-' ? 'glyphicon-sort-by-alphabet-alt' : 'glyphicon-sort-by-alphabet']" aria-hidden="true"></span>
                                    </a>
                                </th>
                                <th>
                                    Price
                                    <!--<a href="#" @click.prevent="sort('price')" style="display:block">-->
                                        <!--Price-->
                                        <!--<span :class="['glyphicon', 'pull-right', sorting.price.direction === '-' ? 'glyphicon-sort-by-order-alt' : 'glyphicon-sort-by-order']" aria-hidden="true"></span>-->
                                    <!--</a>-->
                                </th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="product in items" :key="product.id">
                                <td>#{{ product.id }}</td>
                                <td>{{ product.name }} <span class="label label-danger" v-if="product.discount > 0">-{{ product.discount }} %</span>
                                </td>
                                <td>
                                    <s>{{ product.price | currency }}</s>
                                    <strong>{{ product.price | discount(product.discount) | currency }}</strong>
                                </td>
                                <td>
                                    <button type="button" @click.prevent="buy(product.id)"
                                            class="btn btn-primary btn-sm">
                                        <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Buy
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    const API_PRODUCT_RESOURCE = '/api/products'
    export default {
        data () {
            return {
                items: [],
                sorting: {
                    name: {direction: '+'},
                    price: {direction: '+'}
                }
            }
        },

        filters: {
            discount: (val, discount) => (val - (discount / 100) * val),
            currency: (val, symbl = '$') => `${symbl} ${val.toFixed(2)}`
        },

        methods: {
            buy: function (id) {
                const self = this
                axios.post(`${API_PRODUCT_RESOURCE}/${id}/buy`)
                        .then(function (response) {
//                            self.items = self.items.filter(item => {
//                                return item.id !== id
//                            })
                            self.refresh()
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
            },

            sort: function (name) {
                if(this.sorting[name].direction === '-') {
                    this.sorting[name].direction = '+'
                } else {
                    this.sorting[name].direction = '-'
                }

                this.refresh(name)
            },

            refresh: function (sortBy = null) {
                const self = this
                let params = {}

                if (null !== sortBy) {
                    params = {sort: `${this.sorting[sortBy].direction}${sortBy}`}
                }

                axios.get(API_PRODUCT_RESOURCE, {params: params})
                        .then(function (response) {
                            self.items = response.data.items
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
            }
        },

        created () {
            this.refresh('name')
        }
    }
</script>
