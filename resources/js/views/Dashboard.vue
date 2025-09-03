<template>
  <v-container>
    <h2 class="text-xl font-bold mb-4">Dashboard</h2>
    <div v-if="user">
      <p><strong>ID:</strong> {{ user.id }}</p>
      <p><strong>Name:</strong> {{ user.name }}</p>
      <p><strong>Email:</strong> {{ user.email }}</p>
    </div>
    <v-btn color="grey darken-1" @click="logout" class="mt-4">Logout</v-btn>
  </v-container>
</template>

<script>


export default {
  data() {
    return { user: null };
  },
  mounted() {
    this.fetchProfile();
  },
  methods: {
    async fetchProfile() {
      try {
        const res = await axios.get('/api/me?provider=einvoice');
        this.user = res.data;
      } catch (err) {
        console.error(err);
      }
    },
    async logout() {
      try {
        await axios.post('/auth/logout');
        this.$router.push('/login');
      } catch (err) {
        console.error(err);
      }
    }
  }
}
</script>