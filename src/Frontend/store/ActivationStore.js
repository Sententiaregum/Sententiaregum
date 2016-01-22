/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import FluxEventHubStore from './FluxEventHubStore';
import Portal from '../constants/Portal';

/**
 * Store for the activation process.
 * As the activation api doesn't have any internal data, this is just a wrapper
 * for the dispatch<>component flow.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class ActivationStore extends FluxEventHubStore {
  /**
   * @inheritdoc
   */
  getSubscribedEvents() {
    return [
      { name: Portal.ACTIVATE_ACCOUNT, callback: () => this.emitChange('Activation.Success') },
      { name: Portal.ACTIVATION_FAILURE, callback: () => this.emitChange('Activation.Failure') }
    ];
  }
}

const store = new ActivationStore();
store.init();

export default store;
