<template>
    <div>
        <v-app-bar color="deep-purple accent-4" dense dark>
            <v-toolbar-title><v-btn text color="white" :href="baseUrl">Powerfeed</v-btn></v-toolbar-title>
            <v-spacer></v-spacer>
            <template v-if="user">
                <v-menu left bottom>
                    <template v-slot:activator="{ on, attrs }">
                    <v-btn text color="white" v-bind="attrs" v-on="on">
                        {{ user.first_name }} {{ user.last_name }}
                        <v-icon>mdi-menu-down</v-icon>
                    </v-btn>
                    </template>
                    <v-list>
                        <v-list-item :href="settingsUrl">
                            <v-list-item-title>Settings</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="logout()">
                            <v-list-item-title>Logout</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </template>
        </v-app-bar>
    </div>
</template>

<script>
export default {
    name: "Navbar",
    props: {
        user: {
            required: true
        },
        baseUrl: {
            required: true
        }
    },
    computed: {
        settingsUrl() {
            return this.baseUrl + "/settings"
        },
        logoutUrl() {
            return this.baseUrl + "/logout"
        },
    },
    methods: {
        logout() {
            axios.post(this.logoutUrl).then(() => window.location = this.baseUrl + "/login")
        }
    }
}
</script>

<style scoped>
</style>
