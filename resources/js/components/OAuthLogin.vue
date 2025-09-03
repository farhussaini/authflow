<template>
  <v-container class="text-center mt-20">
    <h1 class="text-xl font-bold mb-4">Login</h1>
    <v-btn color="blue darken-2" @click="login('einvoice')">Login with Einvoice</v-btn>
    <v-btn color="red darken-2" class="ml-4" @click="login('google')">Login with Google</v-btn>
  </v-container>
</template>

<script>
export default {
  methods: {
    login(provider) {
      // Redirect user to Laravel OAuth redirect
      window.location.href = `/auth/redirect?provider=${provider}`;
    }
  }
}
</script>