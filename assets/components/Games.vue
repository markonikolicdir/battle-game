<template>
  <div class="container">
    <div class="row">
        <h2 class="text-center">Battle Simulator - Games</h2>
    </div>
    <div class="row">
      <form class="col-12">
        <div class="input-group">
          <input type="text" class="form-control col-4 mr-1" v-model="game.name">
          <button type="button" class="btn btn-secondary" @click.prevent="createGame">Create Game</button>
          <p class="m-2"> <span class="text-warning">{{message}} </span></p>
        </div>
      </form>
    </div>
    <div class="row">
      <h2>List of games</h2>
      <table class="table table-striped">
        <thead>
        <tr>
          <th>Name</th>
          <th>Status</th>
          <th>Turns</th>
          <th>Add Army</th>
          <th>Battle</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="game in games">
          <td>{{ game.name }}</td>
          <td>
            <span class="text-success" v-if="game.status">Active</span>
            <span v-else>Inactive</span>
          </td>
          <th>{{ game.turns }}</th>
          <td>
            <router-link :to="{ name: 'add-army', params: { gameId: game.id }}">Add Army</router-link>
          </td>
          <td>
            <router-link :to="{ name: 'battle', params: { gameId: game.id }}">Battle</router-link>
          </td>
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
      },
      message: ''
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
          .catch((error) => {
            this.message = error.response.data.errors.join(',')
          });
    }
  }
}
</script>

<style scoped>

</style>
