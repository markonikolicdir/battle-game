<template>
  <div class="container">
    <div class="row">
      <div class="col-6">
        <h2 class="text-center">Battle Simulator - Battles</h2>
      </div>
      <div class="col-3">
        Pages:
        <router-link class="m-2" :to="{ name: 'home' }">Games</router-link>
        <router-link class="" :to="{ name: 'add-army', params: { gameId: id }}">Add Army</router-link>
      </div>
      <div class="col-3">
        Actions:
        <button type="button" class="btn btn-secondary m-2" @click.prevent="turn()">Turn</button>
        <button type="button" class="btn btn-secondary" @click.prevent="autorun()">Autorun</button>
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
      <div class="col-5">
        <h3 class="border-bottom">Armies with their points</h3>
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
      <div class="col-5 offset-1">
        <h3 class="border-bottom">Battle log before every turn</h3>
        <table class="table table-striped">
          <thead>
          <tr>
            <th>Attacker</th>
            <th>Units</th>
            <th>Enemy</th>
            <th>Units</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="battle in battles">
            <td>{{ battle.attacker }}</td>
            <td>{{ battle.attackerUnits }}</td>
            <td>{{ battle.enemy }}</td>
            <td>{{ battle.enemyUnits }}</td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "Battle",
  data() {
    return {
      id: null,
      game: {
        name: '',
        turns: 0,
        winner: null
      },
      armies: [],
      battles: [],
      message: '',
    }
  },
  computed: {
  },
  mounted() {
    this.id = this.$route.params.gameId
    this.getGame()
    this.listArmies()
    this.listBattles()
  },
  methods:{
    turn(){
      axios.get(`/games/${this.id}/turn`)
          .then((response) => {
            this.game.turns = response.data.turns
            this.game.winner = response.data.winner
            this.listArmies()
            this.listBattles()

            this.message = response.data.message
          })
          .catch((error) => {
            this.message = error.response.data.errors.join(',')
          });
    },
    autorun(){
      axios.get(`/games/${this.id}/autorun`)
          .then((response) => {
            this.game.turns = response.data.turns
            this.game.winner = response.data.winner
            this.listArmies()
            this.listBattles()

            this.message = response.data.message
          })
          .catch((error) => {
            this.message = error.response.data.errors.join(',')
          });
    },
    getGame(){
      axios.get(`/games/${this.id}`)
          .then((response) => {
            this.game.name = response.data.name
            this.game.turns = response.data.turns
            this.game.winner = response.data.winner
          })
    },
    listArmies(){
      axios.get(`/games/${this.id}/armies`)
          .then((response) => {
            this.armies = response.data
          })
    },
    listBattles(){
      axios.get(`/games/${this.id}/battles`)
          .then((response) => {
            this.battles = response.data
          })
    },
  }
}
</script>

<style scoped>

</style>