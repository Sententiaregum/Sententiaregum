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

import AppDispatcher from '../../dispatcher/AppDispatcher';
import sinon from 'sinon';
import chai from 'chai';
import ActivationStore from '../../store/ActivationStore';
import Portal from '../../constants/Portal';

describe('ActivationStore', () => {
  it('triggers reactjs changes for dispatching handlings', () => {
    sinon.stub(ActivationStore, 'emitChange', (name) => {
      chai.expect(name).to.equal('Activation.Success')
    });

    AppDispatcher.dispatch({
      event: Portal.ACTIVATE_ACCOUNT
    });

    ActivationStore.emitChange.restore();
  });

  it('triggers error handling', () => {
    sinon.stub(ActivationStore, 'emitChange', (name) => {
      chai.expect(name).to.equal('Activation.Failure')
    });

    AppDispatcher.dispatch({
      event: Portal.ACTIVATION_FAILURE
    });
    ActivationStore.emitChange.restore();
  });
});
