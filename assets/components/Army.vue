<template>
  <div class="container">
    <div class="row">
      <router-link :to="{ name: 'home' }">Games</router-link>
      <router-link :to="{ name: 'battle', params: { gameId: id }}">Battle</router-link>
    </div>
    <div class="row">
      <div class="col-4 col-sm-4 col-lg-4">
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
          </div>
        </form>
      </div>
      <div col-6 col-sm-6 col-lg-6>
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
            <td>{{ army.defeated }}</td>
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
      army:{
        name:null,
        units:0,
        strategy:'Random',
        defeated:0
      },
      armies: []
    }
  },
  computed: {
  },
  mounted() {
    this.id = this.$route.params.gameId,
    this.listArmies()
  },
  methods:{
    addArmy(){
      axios.post(`/games/${this.id}/add-army`,this.army)
          .then((response) => {
            console.log(response)
            this.armies.push(response.data)

            this.army.name = null
            this.army.units = 0
            this.army.strategy = 'Random'
            this.army.defeated = 0
          })
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
