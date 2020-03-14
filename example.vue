<template>
    <div>
        <datatable :config="datatableConfig" :fetchData="fetchData"></datatable>
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
                    url: '/user-datatale',
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
                                vm.fetchData++
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
                            /*
                            searchable: true,
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
        mounted() {
            let vm = this
            // Test send custom params to search
            setTimeout(function () {
                vm.$set(vm.datatableConfig.customParamsRequest, 'type', 'xyz')
                vm.fetchData++
            }, 5000)
        }
    }
</script>
