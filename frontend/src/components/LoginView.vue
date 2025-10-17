<template>
  <div>
    <form @submit.prevent="loginForm">
      <h2>Connexion</h2>
      <p>Connectez-vous pour continuer</p>

      <div>
        <label for="email">Email :</label>
        <input id="email" type="email" placeholder="hermes@gmail.com" v-model="email" />
      </div>

      <div>
        <label for="password">Mot de passe :</label>
        <input id="password" type="password" placeholder="********" v-model="password" />
      </div>

      <div>
        <button type="submit" :disabled="auth.loading">
          <span v-if="!auth.loading">Se connecter</span>
          <span v-else>Chargement...</span>
        </button>
      </div>

      <p>
        Vous n'avez pas encore de compte ?
        <router-link to="/register">Inscrivez-vous ici</router-link>
      </p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/AuthStore';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

const email = ref('');
const password = ref('');

const loginForm = async () => {
  const loginData = {
    email: email.value,
    password: password.value,
  };

  await auth.login(loginData);

  if (auth.success) {
    router.push({ name: 'hello' });
  }
};
</script>
