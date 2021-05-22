<template>
    <div id="grid" class="container-fluid" style="overflow-y: scroll">
        <v-grid
            theme="compact"
            :source="rows"
            :columns="columns"
            resize="true"
        ></v-grid>
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
        columnEdits: {
            required: true
        }
    },
    data() {
        return {
            columns: [],
            rows: [],
            modal: null
        }
    },
    mounted() {
        this.modal = document.querySelector("#editColumnModal")
    },
    watch: {
        feedId: function (id) {
            this.feedId = id
            const self = this
            let newColumns = []
            let newRows = []

            axios
                .get("/feed/" + this.feedId)
                .then(response => {

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
                            bufferObject.[i] = array[i]
                        }
                        newRows.push(bufferObject);
                    })
                    self.columns = newColumns
                    self.rows = newRows
                })
                .catch(error => console.log(error))
        },
        columnEdits: function() {
            const self = this
            let newColumns = []
            let newRows = []

            axios
                .get("/feed/" + this.feedId)
                .then(response => {

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
                            bufferObject.[i] = array[i]
                        }
                        newRows.push(bufferObject);
                    })
                    self.columns = newColumns
                    self.rows = newRows
                })
                .catch(error => console.log(error))
        }
    },
    components: {
        VGrid,
    },
}
</script>

<style scoped>

</style>
