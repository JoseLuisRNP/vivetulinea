import pluginVue from 'eslint-plugin-vue';
import vueTsEslintConfig from '@vue/eslint-config-typescript';
import skipFormatting from 'eslint-config-prettier';

export default [
  {
    name: 'app/files-to-ignore',
    ignores: [
      '**/dist/**',
      '**/dist-ssr/**',
      '**/coverage/**',
      '**/node_modules/**',
      '**/public/build/**',
      '**/public/js/**',
      '**/vendor/**',
      '**/bootstrap/**',
      '**/storage/**',
      '**/database/**',
      '**/app/**',
      '**/config/**',
      '**/routes/**',
      '**/tests/**',
      '**/*.config.js',
      '**/*.config.ts',
    ],
  },
  ...pluginVue.configs['flat/recommended'],
  ...vueTsEslintConfig(),
  skipFormatting,
  {
    files: ['**/*.{js,mjs,cjs,ts,mts,tsx,vue}'],
    rules: {
      'vue/multi-word-component-names': 'off',
      'vue/no-v-html': 'off',
      'vue/require-default-prop': 'off',
      'vue/block-lang': 'off',
      'vue/max-attributes-per-line': [
        'error',
        {
          singleline: 3,
          multiline: 1,
        },
      ],
      '@typescript-eslint/no-explicit-any': 'warn',
      '@typescript-eslint/no-unused-vars': [
        'warn',
        {
          argsIgnorePattern: '^_',
          varsIgnorePattern: '^_',
        },
      ],
      'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
      'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
    },
  },
];





