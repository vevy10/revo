import { computed, ref } from 'vue';
import api from '@/lib/api';

type Role = {
    id: number;
    code: string;
    name: string;
};

type User = {
    id: number;
    first_name: string | null;
    last_name: string | null;
    name: string;
    email: string;
    role: Role;
};

const token = ref<string | null>(localStorage.getItem('auth_token'));
const user = ref<User | null>(null);

function setSession(nextToken: string, nextUser: User) {
    token.value = nextToken;
    user.value = nextUser;
    localStorage.setItem('auth_token', nextToken);
}

function clearSession() {
    token.value = null;
    user.value = null;
    localStorage.removeItem('auth_token');
}

export function useAuth() {
    const isAuthenticated = computed(() => token.value !== null);

    async function login(email: string, password: string) {
        const { data } = await api.post('/auth/login', { email, password });

        setSession(data.token, data.user);

        return data.user as User;
    }

    async function activate(payload: {
        token: string;
        first_name: string;
        last_name: string;
        password: string;
        password_confirmation: string;
    }) {
        const { token: invitationToken, ...body } = payload;
        const { data } = await api.post(`/invitations/${invitationToken}/activate`, body);

        setSession(data.token, data.user);

        return data.user as User;
    }

    async function fetchUser() {
        if (!token.value) {
            return null;
        }

        const { data } = await api.get('/auth/me');
        user.value = data.user;

        return data.user as User;
    }

    async function logout() {
        try {
            await api.post('/auth/logout');
        } finally {
            clearSession();
            window.location.href = '/login';
        }
    }

    return {
        activate,
        fetchUser,
        isAuthenticated,
        login,
        logout,
        token,
        user,
    };
}
