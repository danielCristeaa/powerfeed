<template>
    <div class="row">
        <div class="col-md-2 ml-2">
            <h5>Your Feeds</h5>
            <a class="btn btn-info" data-toggle="modal" data-target="#addFeedModal">+ Add feed</a>
            <div id="feeds">
                <div class="card" v-for="feed in feeds">
                    <div class="card-body" :id="feed['_id']" >
                        {{ feed['name'] }}
                        <button @click="loadFeed(feed['_id'])">Button</button>
                    </div>
                </div>
            </div>
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
            feedId: null
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
            .then(response => (this.feeds = response.data))
            .catch(error => console.log(error))
    },
    methods: {
        loadFeed(id) {
            this.feedId = id;
        }
    }
}
</script>
