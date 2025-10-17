<template>
  <div>
    <form @submit.prevent="registerForm">
      <h2>Créer un compte</h2>
      <p>Créer un compte pour continuer</p>

      <div>
        <label for="name">Nom :</label>
        <input id="name" type="text" placeholder="Hermes" v-model="name" />
      </div>

      <div>
        <label for="email">Email :</label>
        <input id="email" type="email" placeholder="hermes@gmail.com" v-model="email" />
      </div>

      <div>
        <label for="password">Mot de passe :</label>
        <input id="password" type="password" placeholder="********" v-model="password" />
      </div>

      <div>
        <label for="password_confirmation">Confirmer mot de passe :</label>
        <input id="password_confirmation" type="password" placeholder="********" v-model="passwordConfirmation" />
      </div>

      <div>
        <button type="submit" :disabled="auth.loading">
          <span v-if="!auth.loading">S'inscrire</span>
          <span v-else>Chargement...</span>
        </button>
      </div>

      <p>
        Vous avez déjà un compte ?
        <router-link to="/login">Connectez-vous ici</router-link>
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

const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');

const registerForm = async () => {
  const registerData = {
    name: name.value,
    email: email.value,
    password: password.value,
    password_confirmation: passwordConfirmation.value
  };

  await auth.register(registerData);

  if (auth.success) {
    router.push({ name: 'hello' });
  }
};
</script>
