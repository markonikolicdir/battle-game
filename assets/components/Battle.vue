<template>
  <div class="container">
    <div class="row">
      <router-link :to="{ name: 'home' }">Games</router-link>
      <router-link :to="{ name: 'add-army', params: { gameId: id }}">Add Army</router-link>
    </div>
    <div>
      <button type="button" class="btn btn-secondary m-2" @click.prevent="turn()">Turn</button>
      <button type="button" class="btn btn-secondary" @click.prevent="autorun()">Autorun</button>
    </div>
    <div class="row">
      <div class="row">
        <p>Nuber of Turns: {{turns}}</p>
        <p>Message: {{message}}</p>
      </div>
      <div class="col-5 offset-2">
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
            <td>{{ army.defeated }}</td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="col-5">
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
      armies: [],
      battles: [],
      turns: 0,
      message: ''
    }
  },
  computed: {
  },
  mounted() {
    this.id = this.$route.params.gameId
    this.listArmies()
    this.listBattles()
  },
  methods:{
    turn(){
      axios.get(`/games/${this.id}/turn`)
          .then((response) => {
            console.log(response)

            this.turns = response.data.turns
            this.message = response.data.message

            this.listArmies()
            this.listBattles()
          })
    },
    autorun(){
      axios.get(`/games/${this.id}/autorun`)
          .then((response) => {
            console.log(response)

            this.turns = response.data.turns
            this.message = response.data.message

            this.listArmies()
            this.listBattles()
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