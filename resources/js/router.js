import Vue from 'vue';
import Router from 'vue-router';
import homepage from './components/homepage';
import profile from './components/profile';
import chat from './components/chat';
import friends from './components/friends';
import gallery from './components/gallery';
import chatterminal from './components/chat-terminal';
import conversations from './components/conversation';
//--end
Vue.use(Router)

const routes = [
    {
        path: '/',
        component: homepage
    },
    {
        path: '/conversation/:id',
        name:'conversation',
        component: conversations
    },
    {
        path: '/homepage',
        component: homepage
    },
    {
        path: '/profile',
        component: profile
    },
    {
        path: '/messenger',
        component: chat
    },
    {
        path: '/friends',
        component: friends
    },
    {
        path: '/gallery',
        component: gallery
    },
    {
        path: '/scroll',
        component: chatterminal
    },
    {
        path: '/chat/:token',
        name: 'chat',
        component: chatterminal,
    },
    // {
    //     path: '/chat/:id',
    //     name: 'chat',
    //     components: {
    //         default: chat,
    //     },
    //     children: [
    //         {
    //             // A will be rendered in the second <router-view>
    //             // when /your-sidebar-url/a is matched
    //             path: '/chat/:id',
    //             name: 'chat',
    //             components: {
    //                 default: chat,
    //                 chats: chatterminal //note that 'b' is the name of child router view
    //             }
    //         },
 
    //     ]
    // },

] 
export default new Router({
   routes: routes
})
