<template>
    <div>
        <button @click="customSearch">Custom Search</button>
        <datatable :config="datatableConfig" ref="usersDatatable"></datatable>
    </div>
</template>

<script>
    import Datatable from './Datatable'

    export default {
        name: 'Example',
        components: {Datatable},
        data() {
            const vm = this;
            return {
                fetchData: 0,
                datatableConfig: {
                    url: '/users/data',
                    method: 'get',
                    operations: [
                        {
                            type: 'primary',
                            text: 'View',
                            method: function (index, row) {
                                console.log('View', row)
                            },
                        },
                        {
                            type: 'danger',
                            text: 'Delete',
                            method: function (index, row) {
                                console.log('Delete', row)
                                vm.$refs.usersDatatable.loadPage()
                            },
                        }
                    ],
                    columns: [
                        {
                            text: 'User ID',
                            data: 'id',
                            name: 'id',
                            /*
                            searchable: true,
                            orderable: true,
                            */
                        },
                        {
                            text: 'User Name',
                            data: 'name',
                            name: 'name',
                            /*
                            searchable: true,
                            orderable: true,
                            */
                        },
                        {
                            text: 'User Email',
                            data: 'email',
                            name: 'email',
                            searchable: true,
                            /*
                            orderable: true,
                            */
                        }
                    ],
                    order: [
                        {
                            column: 0,
                            dir: 'asc',
                        }
                    ],
                    // custom data will send to request
                    customParamsRequest: {
                        type: 'abc',
                    },
                    setting: {
                        displaySearchInput: true,
                    },
                    handleRowClick: function (row, column, event) {
                        alert('handleRowClick')
                    }
                }
            }
        },
         methods: {
            customSearch() {
                this.$set(this.datatableConfig.customParamsRequest, 'type', 'ccccc')
                this.$refs.usersDatatable.loadPage()
            }
        }
    }
</script>
