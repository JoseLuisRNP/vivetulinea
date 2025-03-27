module.exports = {
    root: true,
    env: {
        browser: true,
        es2021: true,
        node: true,
    },
    extends: [
        'plugin:vue/vue3-recommended',
        'eslint:recommended',
        '@vue/typescript/recommended',
        'plugin:prettier/recommended'
    ],
    parserOptions: {
        ecmaVersion: 2021,
        parser: '@typescript-eslint/parser',
    },
    plugins: ['prettier'],
    rules: {
        'prettier/prettier': 'error',
        'vue/multi-word-component-names': 'off',
        'vue/no-v-html': 'off',
        'vue/require-default-prop': 'off',
        'vue/max-attributes-per-line': ['error', {
            singleline: 3,
            multiline: 1
        }],
        '@typescript-eslint/no-explicit-any': 'warn',
        '@typescript-eslint/no-unused-vars': ['warn', { 
            argsIgnorePattern: '^_',
            varsIgnorePattern: '^_'
        }],
        'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
        'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    },
}; 