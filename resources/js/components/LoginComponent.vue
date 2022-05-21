<template>
    <v-app>
        <navbar-component :user="null" :base-url="null"></navbar-component>
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
                    <v-text-field v-model="email" label="E-mail" name="email"
                        :rules="[rules.required]">
                    </v-text-field>
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
                        @click:append="showPassword = !showPassword"
                        label="Password">
                    </v-text-field>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="4" class="mx-auto">
                    <v-btn color="deep-purple accent-4 white--text" class="mr-4" @click="submit">Login</v-btn>
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
            },
            autofillFixEmail: false,
            autofillFixPassword: false,
        }
    },
    mounted() {
        this.autoLoginCheckingInterface()
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
        },
        //workaround for input bug on browser autocomplete
        autoLoginCheckingInterface() {
            // each 100ms we check if the issue was produced
            let intervalDetectAutofill = setInterval(() => {

                // we target at least one of the stuff that will be affected by autofill
                // to do our checking
                if (document.querySelectorAll('input[type="password"]:-webkit-autofill').length > 0) {
                    // and we inform the system about the issue if it is produced
                    this.autofillFixPassword = true

                    // we stop to check if issue was produced
                    clearInterval(intervalDetectAutofill)
                }
            }, 100)

            let intervalDetectAutofillEmail = setInterval(() => {
                if (document.querySelectorAll('input[type="text"]:-webkit-autofill').length > 0) {
                    this.autofillFixEmail = true
                    clearInterval(intervalDetectAutofillEmail)
                }
            }, 100)

            // if after 3s nothing appear, means no autofill was made
            setTimeout(() => {
                if (intervalDetectAutofill) {
                    clearInterval(intervalDetectAutofill)
                    intervalDetectAutofill = null
                }
                if (intervalDetectAutofillEmail) {
                    clearInterval(intervalDetectAutofillEmail)
                    intervalDetectAutofillEmail = null
                }
            }, 3000)
        },
    },
    watch: {
        autofillFixEmail() {
            let emailInput = document.querySelector('input[type="text"]');
            let emailLabel = emailInput.previousElementSibling;
            emailLabel.classList.add('v-label--active')
        },
        autofillFixPassword() {
            let passwordInput = document.querySelector('input[type="password"]');
            let passwordLabel = passwordInput.previousElementSibling;
            passwordLabel.classList.add('v-label--active')
        }
    }
}
</script>

<style scoped>
</style>
