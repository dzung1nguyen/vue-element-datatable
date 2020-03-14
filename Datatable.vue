<template>
    <div class="datatable-wrapper">
        <div class="datatable-search" v-if="setting.displaySearchInput">
            <el-row type="flex" class="row-bg">
                <el-col :span="8" :offset="16">
                    <el-input type="text" placeholder="Search" v-model="input.search.value"
                              @blur="handleSearch($event)"></el-input>
                </el-col>
            </el-row>
        </div>

        <el-table v-loading="loading" :data="result.data" border style="width: 100%"
                  @row-click="handleRowClick" @sort-change="handleSortChange">
            <template v-for="(column, index) in input.columns">
                <el-table-column :prop="column.data" :label="column.text"
                                 :sortable="column.orderable ? 'custom' : false"></el-table-column>
            </template>
            <template v-if="config.operations.length">
                <el-table-column label="Operations">
                    <template slot-scope="scope">
                        <template v-for="(operation, oindex) in config.operations">
                            <el-button size="mini" :type="operation.type"
                                       @click.stop="operation.method(scope.$index, scope.row)">{{ operation.text }}
                            </el-button>
                        </template>
                    </template>
                </el-table-column>
            </template>
        </el-table>
        <div class="datatable-pagination">
            <el-pagination align="center" background
                           layout="total, sizes, prev, pager, next"
                           :total="result.recordsFiltered"
                           :page-count="totalPage"
                           :current-page.sync="currentPage"
                           :page-sizes="[10, 25, 50, 100]"
                           :page-size="10"
                           :disabled="disabledPagination"
                           @current-change="loadPage"
                           @size-change="handleSizeChange"
            ></el-pagination>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import {toQueryString} from './utils'
    import _ from 'lodash'

    export default {
        name: 'Datatable',
        props: ['config'],
        data() {
            const config = this.config
            // Columns
            const columnDefault = {
                text: '',
                data: '',
                name: '',
                searchable: true,
                orderable: true,
                search: {
                    value: null,
                    regex: false,
                }
            }
            let columns = []
            for (let i = 0; i < config.columns.length; i++) {
                columns[i] = {
                    ...columnDefault, ...config.columns[i]
                }
            }
            // Setting
            let setting = {
                displaySearchInput: true,
            }
            if (config.setting) {
                setting = {
                    ...setting,
                    ...config.setting
                }
            }
            return {
                url: config.url,
                method: config.method || 'get',
                input: {
                    draw: 0,
                    columns,
                    order: config.order || [{column: 0, dir: 'desc',}],
                    start: 0,
                    length: 10,
                    search: {value: '', regex: false},
                },
                result: {
                    queries: [],
                    data: [],
                    draw: 0,
                    recordsTotal: 0,
                    recordsFiltered: 0,
                    '_': 0,
                },
                setting,
                currentPage: 1,
                disabledPagination: false,
                loading: true,
                // keep old search text
                oldSearchText: '',
            }
        },
        computed: {
            totalPage: function () {
                return Math.ceil(this.result.recordsTotal / this.input.length)
            },
            customParamsRequest: function () {
                return this.config.customParamsRequest ? {...this.config.customParamsRequest} : {}
            }
        },
        mounted() {
            this.fetchData()
        },
        methods: {
            async fetchData() {
                const vm = this
                let url = this.url
                let input = {
                    ...this.input,
                    // _params to send custom search
                    _params: {...this.customParamsRequest}
                }
                const method = this.method
                this.loading = true
                this.disabledPagination = true
                if (method === 'get') {
                    let queryString = await toQueryString({...input})
                    url += `?${queryString}`
                }
                const customRequest = {
                    method,
                    url,
                    data: method === 'get' ? {} : {...input}
                }
                try {
                    this.oldSearchText = this.input.search.value
                    this.input.draw++
                    let response = await axios(customRequest)
                    this.$set(this, 'result', {...response.data})
                } catch (e) {
                    console.error(url, e)
                }
                setTimeout(function () {
                    vm.loading = false
                    vm.disabledPagination = false
                }, 500)
            },
            handleSearch(e) {
                e.preventDefault()
                if (this.oldSearchText !== this.input.search.value) {
                    this.loadPage(1)
                }
            },
            loadPage(pageNumber = 1) {
                if (this.currentPage !== pageNumber) {
                    this.currentPage = pageNumber
                }
                this.$set(this.input, 'start', this.input.length * (pageNumber - 1))
                this.fetchData()
            },
            handleSizeChange(size) {
                this.input.length = size
                this.loadPage(1)
            },
            async handleSortChange(sort) {
                let columnIndex = await _.findIndex(this.input.columns, function (o) {
                    return o.data === sort.prop
                })
                if (columnIndex !== -1) {
                    let order = []
                    if (sort.order) {
                        let dir = sort.order === 'ascending' ? 'asc' : 'desc'
                        order = [
                            {column: columnIndex, dir,}
                        ]
                    }
                    this.$set(this.input, 'order', [...order])
                    this.loadPage(1)
                }
            },
            handleRowClick(row, column, event) {
                if (this.config.hasOwnProperty('handleRowClick') && typeof this.config.handleRowClick === 'function') {
                    this.config.handleRowClick(row, column, event)
                }
            },
        }
    }
</script>

<style lang="scss" scoped>
    .datatable-wrapper {
        .datatable-search {
            margin-bottom: 15px;
        }

        .datatable-pagination {
            margin-top: 15px;
        }
    }
</style>
