import { startStimulusApp } from '@symfony/stimulus-bridge';

// Démarre Stimulus et charge les contrôleurs de controllers.json + du dossier controllers/
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));