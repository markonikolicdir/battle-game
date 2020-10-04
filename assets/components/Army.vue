<template>
  <div class="container">
    <div class="row"></div>
    <div class="row">
      <div class="col-4 col-sm-4 col-lg-4">
        <h3 class="border-bottom">Add new army</h3>
        <form class="border-danger">
          <div class="form-group">
            <label for="armyname">Name:</label>
            <input type="text" class="form-control" id="armyname" v-model="addArmy.name">
          </div>
          <div class="form-group">
            <label for="armyunit">Unit:</label>
            <input type="number"  class="form-control" id="armyunit" v-model="addArmy.units">
          </div>
          <div class="form-group">
            <label for="armystrategy">Strategy:</label>
            <select class="form-control" id="armystrategy" v-model="addArmy.strategy">
              <option>Random</option>
              <option>Weakest</option>
              <option>Strongest</option>
            </select>
          </div>
          <div class="form-group">
            <button @click.prevent="addAnArmy()">Add an Army</button>
          </div>
        </form>
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
      addArmy:{
        name:null,
        units:0,
        strategy:'Random'
      }
    }
  },
  computed: {
  },
  mounted() {
    this.id = this.$route.params.gameId

    console.log(this.id)
  },
  methods:{
    addAnArmy(){
      axios.post(`/games/${this.id}/add-army`,this.addArmy)
          .then((response) => {
            console.log(response)
            // this.game = response.data,
                // this.getArmies(this.id)
          })
    }
  }

}
</script>

<style scoped>

</style>
