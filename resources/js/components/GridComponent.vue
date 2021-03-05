<template>
    <div id="grid">
        <v-grid
            theme="compact"
            :source="rows"
            :columns="columns"
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
            columns: [{ prop: "name" }, { prop: "details" }],
            rows: [{
                name: "1",
                details: "Item 1",
            }]
        }
    },
    watch: {
        feedId(id) {
            console.log(id);
            this.feedId = id
            axios
                .get("/feed/"+this.feedId)
                .then(response => (console.log(response)))
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
