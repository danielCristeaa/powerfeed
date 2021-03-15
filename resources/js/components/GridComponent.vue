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
        }
    },
    data() {
        return {
            columns: [],
            rows: [],
        }
    },
    watch: {
        feedId(id) {
            this.feedId = id
            const self = this
            let newColumns = []
            let newRows = []
            axios
                .get("/feed/"+this.feedId)
                .then(response => {
                    response.data[0].forEach(function(element){
                        newColumns.push({ prop: element, name: element, size: 150})
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
