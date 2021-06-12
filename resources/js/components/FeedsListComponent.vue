<template>
    <div class="d-flex" id="wrapper">
        <div class="bg-light border-right" id="sidebar-wrapper">
            <div class="sidebar-heading">Your feeds</div>

            <ul class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action bg-light" data-toggle="modal" data-target="#addFeedModal"><i class="fas fa-plus fa-lg"></i> <b>Add</b></a>
                <a href="#" class="list-group-item list-group-item-action bg-light" :class="{ 'firstFeed' : feedId == feed['_id']}" @click="loadFeed(feed['_id'])" :id="feed['_id']" v-for="(feed, index) in feeds">
                    <span>{{ feed['name'] }}</span>
                    <a style="float:right" data-toggle="modal" data-target="#editFeedModal"><i class="fas fa-edit fa-lg"></i></a>
                    <a style="float:right" @click="refreshFeedData()"><i class="fas fa-lg fa-sync"></i></a>
                    <a style="float:right" @click="publish()"><i class="fas fa-lg fa-solid fa-paper-plane"></i></a>
                </a>
            </ul>
        </div>
        <grid :feedId="returnFeedId" :refreshDataCounter="returnRefreshData" />
        <edit-feed-modal-component :feedId="returnFeedId" @updated-values="updateValuesAfterEdit" @deleted-feed="getUserFeeds"/>
        <add-feed-component @new-feed="getUserFeeds"/>
    </div>
</template>

<script>
import Grid from './GridComponent'
import EditFeedModalComponent from "./EditFeedModalComponent";

export default {
    components: {
        Grid,
        EditFeedModalComponent,
    },
    data() {
        return {
            feeds: [],
            feedId: null,
            firstClick: false,
            refreshDataCounter: 0,
        }
    },
    computed: {
        returnFeedId() {
            return this.feedId;
        },
        returnRefreshData() {
            return this.refreshDataCounter
        }
    },
    mounted() {
        this.getUserFeeds()
    },
    methods: {
        getUserFeeds() {
            axios
                .get("/getUserFeeds")
                .then(response => {
                    this.feeds = response.data
                    this.feedId = this.feeds[0]['_id']
                })
                .catch(error => console.log(error))
        },
        loadFeed(id) {
            this.feedId = id;
            this.firstClick = true;
        },
        updateValuesAfterEdit(newName) {
            document.getElementById(this.feedId).getElementsByTagName('span')[0].innerHTML = newName
        },
        publish() {
            axios
                .post("/sendToGoogleMerchant", {
                    feedId: this.feedId,

                })
                .then(response => {
                    console.log(response.data)
                })
                .catch(error => console.log(error))
        },
        refreshFeedData() {
          this.refreshDataCounter++;
          console.log(this.refreshDataCounter)
        }
    }
}
</script>

<style scoped>
.firstFeed {
    background-color: #dae0e5 !important;
}
.fa-sync, .fa-paper-plane {
    margin-right: 6px;
}
</style>
