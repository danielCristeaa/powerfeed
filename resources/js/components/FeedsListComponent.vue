<template>
    <div class="d-flex" id="wrapper">
        <div class="bg-light border-right" id="sidebar-wrapper">
            <div class="sidebar-heading">Your feeds</div>

            <ul class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action bg-light" data-toggle="modal" data-target="#addFeedModal"><i class="fas fa-plus fa-lg"></i> <b>Add</b></a>
                <a href="#" class="list-group-item list-group-item-action bg-light" :class="{ 'firstFeed' : feedId == feed['_id']}" @click="loadFeed(feed['_id'])" :id="feed['_id']" v-for="(feed, index) in feeds">
                    {{ feed['name'] }}
                    <a style="float:right" data-toggle="modal" data-target="#addFeedModal"><i class="fas fa-edit fa-lg"></i></a>
                </a>
            </ul>
        </div>
        <grid :feedId="returnFeedId" />
    </div>
</template>

<script>
import Grid from './GridComponent'

export default {
    components: {
        Grid
    },
    data() {
        return {
            feeds: [],
            feedId: null,
            firstClick: false,
        }
    },
    computed: {
        returnFeedId() {
            return this.feedId;
        }
    },
    mounted() {
        axios
            .get("/getUserFeeds")
            .then(response => {
                this.feeds = response.data
                this.feedId = this.feeds[0]['_id']
            })
            .catch(error => console.log(error))
    },
    methods: {
        loadFeed(id) {
            this.feedId = id;
            this.firstClick = true;
        }
    }
}
</script>

<style scoped>
.firstFeed {
    background-color: #dae0e5 !important;
}
</style>
