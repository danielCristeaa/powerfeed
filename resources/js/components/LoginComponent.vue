<template>
    <v-app>
        <v-form ref="form" @submit.prevent v-model="valid" lazy-validation class="my-auto">
            <v-row>
                <v-col cols="4" class="mx-auto">
                    <v-alert :value="!valid" type="error" dark>
                        Invalid credentials!
                    </v-alert>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="4" class="mx-auto">
                    <v-text-field v-model="email" label="E-mail" name="email" required
                        placeholder=" "
                        persistent-placeholder></v-text-field>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="4" class="mx-auto">
                    <v-text-field
                        v-model="password"
                        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        :type="showPassword ? 'text' : 'password'"
                        :rules="[rules.required]"
                        name="password"
                        counter
                        placeholder=" "
                        persistent-placeholder
                        @click:append="showPassword = !showPassword"
                        label="Password">
                    </v-text-field>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="4" class="mx-auto">
                    <v-btn color="success" class="mr-4" @click="submit">Login</v-btn>
                </v-col>
            </v-row>
        </v-form>
    </v-app>

</template>

<script>
export default {
    name: "Login",
    data() {
        return {
            email: '',
            password: '',
            valid: true,
            showPassword: false,
            rules: {
                required: value => !!value || 'Required.',
            }
        }
    },
    methods: {
        submit() {
            let data = { email: this.email, password: this.password }
            let self = this
            axios.post('/login', data)
            .then(function () {
                window.location = "/feeds"
            })
            .catch(function (error) {
                self.valid = false
            });
        }
    }
}
</script>

<style scoped>
</style>
