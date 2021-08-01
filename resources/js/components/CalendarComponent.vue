<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        
        <form @submit.prevent>
          <div class="form-group">
            <label for="event_name">Event</label>
            <input type="text" id="event_name" class="form-control" v-model="newEvent.event_name">
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="from_date">From</label>
                <input
                  type="date"
                  id="from_date"
                  class="form-control"
                  v-model="newEvent.from_date"
                >
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="to_date">To</label>
                <input type="date" id="to_date" class="form-control" v-model="newEvent.to_date">
              </div>
            </div>
            <div class="col-md-12">
            <span>Day/s Of Week</span>
            </div>
            <div class="col-md-12 mb-4">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="monday" value="1" v-model="newEvent.dow">
                <label class="form-check-label" for="monday">Mon</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="tuesday" value="2" v-model="newEvent.dow">
                <label class="form-check-label" for="tuesday">Tues</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"  id="wednesday" value="3" v-model="newEvent.dow">
                <label class="form-check-label" for="wednesday">Wed</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"  id="thursday" value="4" v-model="newEvent.dow">
                <label class="form-check-label" for="thursday">Thurs</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"  id="friday" value="5" v-model="newEvent.dow">
                <label class="form-check-label" for="friday">Fri</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"  id="saturday" value="6" v-model="newEvent.dow">
                <label class="form-check-label" for="saturday">Sat</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"  id="sunday" value="0" v-model="newEvent.dow">
                <label class="form-check-label" for="sunday">Sun</label>
              </div>
            </div>
            
            <div class="col-md-6 mb-4" v-if="addingMode">
              <button class="btn btn-sm btn-primary" @click="addNewEvent">Save</button>
            </div>
            <template v-else>
              <div class="col-md-6 mb-4">
                <!-- <button class="btn btn-sm btn-success" @click="updateEvent">Update</button> -->
                <button class="btn btn-sm btn-primary" @click="updateEvent">Save</button>
                <button class="btn btn-sm btn-danger d-none" @click="deleteEvent">Delete</button>
                <button class="btn btn-sm btn-secondary d-none" @click="addingMode = !addingMode">Cancel</button>
              </div>
            </template>
          </div>
        </form>
        <flash-message class="animate__animated animate__fadeIn animate__delay-1s"></flash-message>
      </div>
      <div class="col-md-7">
        <FullCalendar  :options="calendarOptions" />
      </div>
    </div>
  </div>
</template>
<script>
import Fullcalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import axios from "axios";
import 'animate.css';
import Vue from 'vue';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
Vue.use(VueSweetalert2);
import VueFlashMessage from 'vue-flash-message';
Vue.use(VueFlashMessage, {
  messageOptions: {
    timeout: 3000
  }
});
require('vue-flash-message/dist/vue-flash-message.min.css');

export default {
  components: {
    Fullcalendar
  },
  data() {
    return {
      calendarOptions: {
        plugins: [ dayGridPlugin, interactionPlugin ],
        initialView: 'dayGridMonth',
        events: "",
        eventClick: this.showEvent
                   
      },
      newEvent: {
        event_name: "",
        from_date: "",
        to_date: "",
        dow: [],
      },
      addingMode: true,
      indexToUpdate: "",
      errors: []
    };
  },
  created() {
    this.getEvents();
  },
  methods: {
    
    addNewEvent: function (e) {
      this.errors = [];
      var errors = "";
      var valid = true;
      if (!event_name.value) {
        this.flashError('Event Name required');
        valid = false;
      }
      if (!from_date.value) {
        this.flashError('Start Date required');
        valid = false;
      }
      if (!to_date.value) {
        this.flashError('End Date required');
        valid = false;
      }

      var isChecked=false;

      $('.form-check-input').each(function(){
        console.log($(this).is(':checked'));
          if ($(this).is(':checked')){
              isChecked=true;
              return;
          }   
      })
      if (!isChecked) {
        this.flashError('Day/s of Week required');
        valid = false;
      }
      if(valid){
        //console.log(this.newEvent.dow);
        axios
          .post("/api/calendar", {
            //...this.newEvent
            event_name: event_name.value,
            from_date: from_date.value,
            to_date: to_date.value,
            days_of_week: JSON.stringify(this.newEvent.dow)
          })
          .then(data => {
            this.getEvents(); // update our list of events
            //this.resetForm(); // clear newEvent properties (e.g. title, from_date and to_date)
            this.showEvent(data.data.data.id);
            //console.log(data.data.data.id);
            Vue.swal({
                      icon: 'success',
                      title: 'Success',
                      text: 'New event created successfully',
                      showConfirmButton: false,
                      timer: 2000
                    });
          })
          .catch(err =>
            //console.log("Unable to add new event!", err.response.data)
            Vue.swal({
                      icon: 'error',
                      title: 'Unable to add new event!',
                      text: 'err.response.data',
                      showConfirmButton: false,
                      timer: 2000
                    })
          );
      }
      
    },
    showEvent(arg) {
      if(arg % 1 === 0){
        this.addingMode = false;
        this.indexToUpdate = arg;
      }else{
        console.log(arg);
        this.addingMode = false;
        const { id, title, startRecur, endRecur, daysOfWeek } = this.calendarOptions.events.find(
          event => event.id === +arg.event.id
        );
        this.indexToUpdate = id;
        this.newEvent = {
          event_name: title,
          from_date: startRecur,
          to_date: endRecur,
          dow: JSON.parse(daysOfWeek)
        };

        console.log(daysOfWeek);
      }
    },
    updateEvent() {
      //console.log(this.newEvent);
      axios
        .put("/api/calendar/" + this.indexToUpdate, {
          //...this.newEvent
          event_name: event_name.value,
          from_date: from_date.value,
          to_date: to_date.value,
          days_of_week: JSON.stringify(this.newEvent.dow)
        })
        .then(resp => {
          //this.resetForm();
          this.getEvents();
          //this.addingMode = !this.addingMode;
          Vue.swal({
                      icon: 'success',
                      title: 'Success',
                      text: 'Event updated successfully',
                      showConfirmButton: false,
                      timer: 2000
                    });
        })
        .catch(err =>
          //console.log("Unable to update event!", err.response.data)
          Vue.swal({
                    icon: 'error',
                    title: 'Unable to update event!',
                    text: 'err.response.data',
                    showConfirmButton: false,
                    timer: 2000
                  })
        );
    },
    deleteEvent() {
      axios
        .delete("/api/calendar/" + this.indexToUpdate)
        .then(resp => {
          this.resetForm();
          this.getEvents();
          this.addingMode = !this.addingMode;
           Vue.swal({
                      icon: 'success',
                      title: 'Success',
                      text: 'Event deleted successfully',
                      showConfirmButton: false,
                      timer: 2000
                    });
        })
        .catch(err =>
          //console.log("Unable to delete event!", err.response.data)
          Vue.swal({
                    icon: 'error',
                    title: 'Unable to delete event!',
                    text: 'err.response.data',
                    showConfirmButton: false,
                    timer: 2000
                  })
        );
    },
    getEvents() {
      axios
        .get("/api/calendar")
        .then(resp => (this.calendarOptions.events = resp.data.data))
        .catch(err => 
          //console.log(err.response.data)
          Vue.swal({
                    icon: 'error',
                    title: 'Unable to get events!',
                    text: 'err.response.data',
                    showConfirmButton: false,
                    timer: 2000
                  })
        );
    },
    resetForm() {
      Object.keys(this.newEvent).forEach(key => {
        return (this.newEvent[key] = "");
      });
    }
  },
  watch: {
    indexToUpdate() {
      return this.indexToUpdate;
    }
  }
};
</script>

<style lang="css">
@import "~@fullcalendar/core/main.css";
@import "~@fullcalendar/daygrid/main.css";
.fc-event-title {
  color: #fff;
}
.fc-event-title:hover {
  cursor: pointer;
}
</style>