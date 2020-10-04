<template>
  <div>
    <h1 class="text-center">Welcome to the ancient battle of {{game.name}}</h1>
    <h5 class="text-center">Yo are a brave soul standing in this dangerous ground - <b>Status: {{game.status}}</b></h5>
    <div class="row border-top" style="height: 50px"></div>
    <div class="row">
      <div class="col-4 col-sm-4 col-lg-4">
        <h3 class=" border-bottom">Add new army</h3>
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
              <option>RANDOM</option>
              <option>WEAKEST</option>
              <option>STRONGEST</option>
            </select>
          </div>
          <div class="form-group">
            <button @click.prevent="addAnArmy()">Add an Army</button>
          </div>
        </form>
        <div class="row" v-if="this.armies.length > 4">
          <button v-if="game.status === 'START'" type="button" class="btn btn-primary btn-lg btn-block btn-dark" @click="startBattle"> Start Battle</button>
          <div class="row col-12" v-if="game.status !== 'START'">
            <button :disabled="automatic" type="button" class="btn btn-primary btn-lg btn-block btn-dark" @click="restartBattle"> Restart Battle</button>
            <button v-if="automatic" type="button" class="btn btn-primary btn-lg btn-block btn-dark" @click="pause"> Pause Battle</button>
            <button type="button" :disabled="winner !== null" class="btn btn-primary btn-lg btn-blue m-2" @click="turn">{{turnNumber + 1}}. Turn </button>
            <button v-if="!automatic" :disabled="winner !== null" type="button" class="btn btn-primary btn-lg btn-blue m-2" @click="automaticPlay"> Quick battle </button>
          </div>
        </div>
        <div class="text-lg-center border-bottom" v-else>
          <b>In order to start the battle 5 armies must be added</b>
        </div>
      </div>
      <div class="col-8 col-sm-8 col-lg-8">
        <div style="margin-bottom: 30px;">
          <h3 class=" border-bottom">Armies</h3>
          <div class="container border">
            <div class="row" v-for="army in armies">
              <div class="col-4 col-sm-4 col-lg-4"><b>{{army.name}}</b></div>
              <div class="col-4 col-sm-4 col-lg-4">units: {{army.units}}</div>
              <div class="col-4 col-sm-4 col-lg-4">strategy: {{army.strategy}}</div>
            </div>
          </div>
        </div>
        <div>
          <h1 class="text-danger text-bold" v-if="winner !== null"> Winner is {{winner.name}}</h1>
        </div>
        <div>
          <h4>Battle log</h4>
          <div class="">
            <ul class="list-group list-group-flush" v-for="(logturn) in logs">
              <li class="list-group-item" v-for="logitem in logturn.log">
                Army: {{logitem.attacker}}
                attacks {{logitem.defender}}
                number of attacks: {{logitem.numberOfAttacks}}
                success attacks: {{logitem.successAttacks}}
                damage: {{logitem.totalDamage}}
              </li>
              <div class="border-bottom">{{logturn.turn}}. turn done</div>
            </ul>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import battle from '../lib/battleActions'
export default {
  name: "ManageGame",
  data() {
    return {
      id: null,
      game: {
        name:null,
        status:'START'
      },
      addArmy:{
        name:null,
        units:0,
        strategy:'RANDOM'
      },
      armies: [],
      logs:[],
      turnNumber:0,
      winner:null,
      automatic:false
    }
  },
  computed: {
  },
  mounted() {
    this.id = this.$route.params.gameId
    this.getGame()
    this.getArmies()
  },
  methods:{
    getGame(){
      axios.get(`/game/${ this.id}`)
          .then((response) => {
            this.game = response.data;
            if(this.game.status !== 'START') {
              this.getLog()
            }
          })
    },
    getArmies(){
      axios.get(`/game/${ this.id}/army`)
          .then((response) => {
            this.armies = response.data
          })
    },
    getLog(){
      axios.get(`/game/${this.id}/log`)
          .then((response) => {
            this.logs = response.data;
            this.loggedArmiesDamage();
            this.checkStatus()
          })
    },
    addAnArmy(){
      axios.post(`/game/${this.id}/army`,this.addArmy)
          .then((response) => {
            this.game = response.data,
                this.getArmies(this.id)
          })

    },
    updateArmy(id, peyload){
      axios.put(`/game/${this.id}/army/${id}`,peyload)
          .then((response) => {
            this.bindWinnerToArmy(this.id)
          })

    },
    restartBattle(){
      axios.put(`/game/${this.id}/restart`,
          {
            status:"START"
          }
      )
          .then((response) => {
            this.game = response.data;
            this.logs = this.game.logs;
            this.armies = this.game.armies;
            this.turnNumber = 0;
            this.winner = null;
          })
    },
    startBattle(){

      if(this.game.status === "START"){
        this.game.status = "PROCESS"
        this.updateGame();
      }

    },
    updateGame(){
      axios.put(`/game/${ this.id}`, this.game)
          .then((response) => {
            this.game = response.data
          })
    },
    turn(){
      this.turnNumber++
      let army = [].slice.call(this.armies);
      let log = [];
      this.armies.map(function(currentAttacker){
        if(currentAttacker.units > 0) {
          let defender = battle.defender(army, currentAttacker);
          let attack = battle.attack(currentAttacker)
          attack.attacker = currentAttacker.name
          attack.defender = defender.name
          attack.defenderId = defender.id
          log.push(attack)
        }
      })

      this.armiesDamage(log)

      this.createLog(log);

      this.logs.push({log:log, turn: this.turnNumber.valueOf()});

      this.findWinner()

    },
    armiesDamage(log){
      this.armies.map(function(army){
        log.map(function(damagedArmy){
          if(army.id === damagedArmy.defenderId){
            let rampage = army.units - damagedArmy.totalDamage;
            army.units = rampage < 1 ? 0 : rampage
          }
        });
      })
    },
    loggedArmiesDamage(){
      let self = this
      this.logs.map(function (log) {
        self.armiesDamage(log.log)
      })
      if(this.logs.length > 0) {
        this.turnNumber = this.logs[this.logs.length - 1].turn
      }
    },
    createLog(log){
      axios.post(`/game/${this.id}/log`,
          {
            turn:this.turnNumber,
            log:log
          }
      )
          .then((response) => {
            console.log(response.data)
          })
    },
    findWinner(){
      let armies = [].slice.call(this.armies);
      let filteredArmies = armies.filter(function(army){
        return army.units > 0
      })

      if(filteredArmies.length === 1){
        this.winner = filteredArmies[0]
        this.automatic = false;
        this.game.status = "FINISH"
        this.updateGame()
        this.updateArmy(this.winner.id, {winner:true})
      }
    },
    automaticPlay(){
      this.automatic = true;
      let i = 1
      while ((this.winner === null && this.automatic === true) || i < 100 ) {
        i++
        this.turn()
      }

    },
    pause(){
      this.automatic = false;

    },
    bindWinnerToArmy(id){
      this.armies.map(function (army) {
        if(army.id === id){
          army.winner = true
        }
      })
    },
    checkStatus(){
      if(this.game.status === 'FINISH'){
        let winnerArmy = null;
        console.log(this.armies);
        this.armies.map(function (army) {
          if(army.winner){
            winnerArmy = army
          }
        })
        this.winner = winnerArmy
      }
    }
  }

}
</script>

<style scoped>

</style>
