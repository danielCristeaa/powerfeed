<template>
    <v-app>
        <div class="d-flex" id="wrapper">
            <div class="bg-light border-right" id="sidebar-wrapper">
                <div class="text-h5 font-weight-regular text-center my-3">Your Google Feeds</div>

                <add-feed-component @new-feed="getUserFeeds"/>
                <v-tabs vertical>
                    <v-tab v-for="feed in feeds" :key="feed['_id']" :class="{ 'firstFeed' : feedId == feed['_id']}" @click="loadFeed(feed['_id'])" :id="feed['_id']">
                        <span>{{ feed['name'] }}</span>
                        <div class="ml-auto">
                            <!-- <v-btn icon data-toggle="modal" data-target="#editFeedModal"><i class="fas fa-edit fa-lg"></i></v-btn> -->
                            <edit-feed-modal-component :feedId="feed['_id']" @updated-values="updateValuesAfterEdit" @deleted-feed="getUserFeeds" @new-columns-added="newColumnsAdded"/>
                            <v-btn icon @click="refreshFeedData()"><i class="fas fa-lg fa-sync"></i></v-btn>
                            <v-btn icon @click="publish()"><i class="fas fa-lg fa-solid fa-paper-plane"></i></v-btn>
                        </div>
                    </v-tab>
                </v-tabs>
            </div>
            <grid :feedId="returnFeedId" :refreshDataCounter="returnRefreshData" :queryDbDataCounter="returnQueryDbDataCounter" />
            <notifications position="bottom right"/>
            <notifications group="processing" position="bottom right"/>
        </div>
    </v-app>
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
        updateValuesAfterEdit(eventObject) {
            document.getElementById(eventObject.feedId).getElementsByTagName('span')[0].innerHTML = eventObject.newName
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
#sidebar-wrapper {
    border-right: 1px solid gray;
    height: 100vh;
}
</style>
