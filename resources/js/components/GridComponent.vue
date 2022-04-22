<template>
    <v-data-table
        :items="rows"
        :headers="columns"
        class="elevation-1"
        :calculate-widths="true"
        hide-default-header>
        <template v-slot:header="{ props }">
            <thead class="v-data-table-header">
                <tr>
                    <th class="table-header" v-for="head in props.headers" :key="head.text">{{ head.text }}
                    <edit-column-component :feedId="feedId" :columnName="head.text" @update-columns="updateColumns"/>
                    </th>
                </tr>
            </thead>
        </template>
        <template v-slot:item.description="{ item }">
            <td class="truncate">
                {{ item.description }}
            </td>
        </template>
    </v-data-table>
</template>

<script>
export default {
    name: "Grid",
    props: {
        feedId: {
            required: true
        },
        refreshDataCounter: {
            required: true
        },
        queryDbDataCounter: {
            required: true
        }
    },
    data() {
        return {
            columns: [],
            rows: [],
            modal: null,
            openEditColumnModal: 0,
        }
    },
    methods: {
        updateColumns() {
            const self = this
            let newColumns = []
            let newRows = []

            axios
                .get("/feed/" + this.feedId)
                .then(response => {
                    if(response.data.success == false) {
                        this.$notify({
                            title: 'Error',
                            text: response.data.data.message,
                            type: 'error',
                            duration: 3000,
                        })

                        return
                    }

                    if(response.data.data.length == 0) {
                        this.columns = []
                        this.rows = []
                        return
                    }
                    response.data.data[0].forEach(function(element) {
                        newColumns.push({ prop: element,
                            text: element,
                            align: 'start',
                            sortable: false,
                            value: element,
                        })
                    })
                    response.data.data[1].forEach(function(array) {
                        let bufferObject = {}
                        for(let i in array) {
                            bufferObject[i] = array[i]
                        }
                        newRows.push(bufferObject);
                    })
                    self.columns = newColumns
                    self.rows = newRows
                })
                .catch(error => console.log(error))
        }
    },
    watch: {
        feedId: function (id) {
            this.feedId = id
            this.updateColumns()
        },
        refreshDataCounter: function() {
            const self = this
            axios
                .get("/refreshData/" + this.feedId)
                .then(response => {
                    if("error" in response.data) {
                        self.$notify({
                            title: 'Error',
                            text: response.data['error'],
                            type: 'error',
                            duration: 3000,
                        })
                    }
                    else {
                        self.$notify({
                            title: 'Success',
                            text: response.data['success'],
                            type: 'success',
                            duration: 3000,
                        })
                        this.updateColumns()
                    }
                })
                .catch(error => console.log(error))
        },
        queryDbDataCounter: function() {
            this.updateColumns()
        }
    },
}
</script>

<style scoped>
.v-data-table {
    overflow-x: auto;
    overflow-y: auto;
    padding-right: 0;
    max-height: 100vh;
}
.truncate {
    max-width: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.table-header {
    font-size: .75rem;
    user-select: none;
    text-align: start !important;
    color: rgba(0,0,0,.6);
    width: 200px;
    min-width: 200px;
    height: 50px;
    padding: 0 16px;
}
</style>
