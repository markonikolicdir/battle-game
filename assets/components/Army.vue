<template>
  <div class="container">

    <div class="row">
      <div class="col-6">
        <h2 class="text-center">Battle Simulator - Army</h2>
      </div>
      <div class="col-6">
        Pages:
        <router-link class="m-2" :to="{ name: 'home' }">Games</router-link>
        <router-link :to="{ name: 'battle', params: { gameId: id }}">Battle</router-link>
      </div>
    </div>
    <div class="row">
      <table class="table table-striped">
        <thead>
        <tr>
          <th>Game</th>
          <th>Status</th>
          <th>Turns</th>
          <th>Winner</th>
        </tr>
        </thead>
        <tbody>
        <tr>
          <td>{{game.name}}</td>
          <td>
            <span class="text-success" v-if="game.status">Active</span>
            <span v-else>Inactive</span>
          </td>
          <td>{{game.turns}}</td>
          <td><span class="text-success">{{game.winner}}</span></td>
        </tr>
        </tbody>
      </table>
      <p>Message: <span class="text-warning"> {{message}} </span></p>
    </div>
    <div class="row">
      <div class="col-4">
        <h3 class="border-bottom">Add new army</h3>
        <form class="border-danger">
          <div class="form-group">
            <label for="armyname">Name:</label>
            <input type="text" class="form-control" id="armyname" v-model="army.name">
          </div>
          <div class="form-group">
            <label for="armyunit">Unit:</label>
            <input type="number"  class="form-control" id="armyunit" v-model="army.units">
          </div>
          <div class="form-group">
            <label for="armystrategy">Strategy:</label>
            <select class="form-control" id="armystrategy" v-model="army.strategy">
              <option>Random</option>
              <option>Weakest</option>
              <option>Strongest</option>
            </select>
          </div>
          <div class="form-group">
            <button type="button" class="btn btn-secondary" @click.prevent="addArmy()">Add Army</button>
            <p>{{message}}</p>
          </div>
        </form>
      </div>
      <div class="col-5 offset-1">
        <h3 class="border-bottom">List of armies</h3>
        <table class="table table-striped">
          <thead>
          <tr>
            <th>Name</th>
            <th>Units</th>
            <th>Strategy</th>
            <th>Defeated</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="army in armies">
            <td>{{ army.name }}</td>
            <td>{{ army.units }}</td>
            <td>{{ army.strategy }}</td>
            <td>
              <span class="text-success" v-if="!army.defeated">Active</span>
              <span class="text-danger" v-else>Defeated</span>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: "Army",
  data() {
    return {
      id: null,
      game: {
        name: '',
        turns: 0,
        winner: null
      },
      army:{
        name:'',
        units:0,
        strategy:'Random',
        defeated:0
      },
      armies: [],
      message: ''
    }
  },
  computed: {
  },
  mounted() {
    this.id = this.$route.params.gameId
    this.getGame()
    this.listArmies()
  },
  methods:{
    getGame(){
      axios.get(`/games/${this.id}`)
          .then((response) => {
            this.game.name = response.data.name
            this.game.turns = response.data.turns
            this.game.winner = response.data.winner
          })
    },
    addArmy(){
      axios.post(`/games/${this.id}/add-army`,this.army)
          .then((response) => {
              this.armies.push(response.data);
              this.army.name = null;
              this.army.units = 0;
              this.army.strategy = 'Random';
              this.army.defeated = 0;
          })
          .catch((error) => {
            this.message = error.response.data.errors.join(',')
          });
    },
    listArmies(){
      axios.get(`/games/${this.id}/armies`)
          .then((response) => {
            this.armies = response.data
          })
    },
  }

}
</script>

<style scoped>

</style>
