<template>
    <div id="grid" class="container-fluid">
        <v-grid
            theme="compact"
            :source="rows"
            :columns="columns"
            resize="true"
            readonly="true"
        ></v-grid>
        <edit-column-component :feedId="feedId" :openEditColumnModal="openEditColumnModal" :modal="modal" @update-columns="updateColumns"/>
    </div>

</template>

<script>
import VGrid from "@revolist/vue-datagrid";

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
    mounted() {
        this.modal = document.querySelector("#editColumnModal")
    },
    methods: {
        updateColumns() {
            const self = this
            let newColumns = []
            let newRows = []

            axios
                .get("/feed/" + this.feedId)
                .then(response => {
                    if(response.data.length == 0) {
                        this.columns = []
                        this.rows = []
                        return
                    }
                    response.data[0].forEach(function(element) {
                        newColumns.push({ prop: element,
                            name: element,
                            size: 150,
                            columnTemplate: (createElement, column) => {
                                return [createElement('div', {
                                }, column.name),
                                    createElement('a', {
                                        style: {
                                            position: 'absolute',
                                            top: '0px',
                                            right: '10px'
                                        },
                                        onclick: function () {
                                            self.modal.classList.add('show')
                                            self.modal.style.paddingRight = "17px"
                                            self.modal.style.display = "block"
                                            self.openEditColumnModal++;

                                            let input = document.querySelector("input[name='newColumnName']")

                                            setTimeout(() => { self.modal.style.opacity = 1; }, 5);

                                            input.value = column.name
                                        },

                                    }, createElement('i', {
                                        class: {
                                            'fa': true,
                                            'fa-cog': true
                                        },
                                        style: {
                                            width: '10px',
                                            height: '10px'
                                        }
                                    }))];
                            },
                        })
                    })
                    response.data[1].forEach(function(array) {
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
    components: {
        VGrid,
    },
}
</script>

<style scoped>
#grid {
    overflow-x: auto;
    overflow-y: auto;
    padding-right: 0;
}
</style>
