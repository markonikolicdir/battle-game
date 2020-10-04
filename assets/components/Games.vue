<template>
  <div class="align-content-center">
    <div class="container align-items-center">
      <h1 class="text-center">Battle Simulator - Games</h1>
      <form>
        <div class="input-group">
          <input type="text" class="form-control col-3 mr-1" v-model="game.name">
          <button type="button" class="btn btn-secondary" @click.prevent="createGame">Create Game</button>
        </div>
      </form>
    </div>
    <div class="container">
      <h2>List of games</h2>
      <table class="table table-striped">
        <thead>
        <tr>
          <th>Name</th>
          <th>Action</th>
          <th>Add Army</th>
          <th>Battle</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="game in games">
          <td>{{ game.name }}</td>
          <td>{{ game.id }}</td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'Games',
  data(){
    return {
      games:[],
      game:{
        name:''
      }
    }
  },
  mounted() {
    this.getGames()
  },
  methods:{
    getGames(){
      axios.get(`/games`)
          .then((response) => {
            this.games = response.data
          })
    },
    createGame(){
      axios.post('/games', this.game)
          .then((response) => {
            this.games.push(response.data)
            this.game.name = ''
          })
          .catch(e => (this.error(e)))
    }
  }
}
</script>

<style scoped>

</style>
