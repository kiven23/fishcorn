const axios = require('axios').default;
export const renderhome = {
 
    mounted() {
      
        axios.get('http://localhost:8001/api/home').then((response)=>{
            this.username = response.data.info.username;
            this.email = response.data.email;
            this.age = response.data.info.age;
            this.profile = response.data.info.profile;
            this.userid = response.data.id;
          });
      }
}



