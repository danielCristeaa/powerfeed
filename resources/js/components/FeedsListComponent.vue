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
        <grid :feedId="returnFeedId" :refreshDataCounter="returnRefreshData" :queryDbDataCounter="returnQueryDbDataCounter" />
        <edit-feed-modal-component :feedId="returnFeedId" @updated-values="updateValuesAfterEdit" @deleted-feed="getUserFeeds" @new-columns-added="newColumnsAdded"/>
        <add-feed-component @new-feed="getUserFeeds"/>
        <notifications position="bottom right"/>
        <notifications group="processing" position="bottom right"/>
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
            queryDbDataCounter: 0,
        }
    },
    computed: {
        returnFeedId() {
            return this.feedId;
        },
        returnRefreshData() {
            return this.refreshDataCounter
        },
        returnQueryDbDataCounter() {
            return this.queryDbDataCounter
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
                    if(response.data.success) {
                        if(response.data.data.length > 0) {
                            this.feeds = response.data.data
                            this.feedId = this.feeds[0]['_id']
                        }
                        else {
                            this.feeds = null
                            this.feedId = null
                        }
                    } else {
                        this.$notify({
                            title: 'Error',
                            text: response.data.data.message,
                            type: 'error',
                            duration: 3000,
                        })
                    }

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
            const self = this
            axios
                .post("/sendToGoogleMerchant", {
                    feedId: this.feedId,
                })
                .then(response => {
                    if(response.data.success) {
                        self.$notify({
                            title: 'Success',
                            text: response.data.message,
                            type: 'success',
                            duration: 3000,
                        })
                    }
                    else {
                        let errors = JSON.parse(response.data.message).error.errors
                        for(let i = 0; i < errors.length; i++) {
                            self.$notify({
                                title: 'Error',
                                text: errors[i].message,
                                type: 'error',
                                duration: 3000,
                            })
                        }
                    }
                })
                .catch(error => console.log(error))
        },
        refreshFeedData() {
          this.refreshDataCounter++;
        },
        newColumnsAdded() {
            this.queryDbDataCounter++
        }
    }
}
</script>

<style>
.firstFeed {
    background-color: #dae0e5 !important;
}
.fa-sync, .fa-paper-plane {
    margin-right: 6px;
}
.vue-notification {
    font-size: 16px;
}
</style>
