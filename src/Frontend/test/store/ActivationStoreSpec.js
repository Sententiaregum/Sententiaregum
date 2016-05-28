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

import { runAction } from 'sententiaregum-flux-container';
import { expect } from 'chai';
import ActivationStore from '../../store/ActivationStore';
import { ACTIVATE_ACCOUNT, ACTIVATION_FAILURE } from '../../constants/Portal';

describe('ActivationStore', () => {
  it('triggers reactjs changes for dispatching handlings', () => {
    runAction(() => {
      return dispatch => dispatch(ACTIVATE_ACCOUNT, {});
    }, []);
    expect(ActivationStore.getState().success).to.equal(true);
  });

  it('triggers error handling', () => {
    runAction(() => {
      return dispatch => dispatch(ACTIVATION_FAILURE, {});
    }, []);
    expect(ActivationStore.getState().success).to.equal(false);
  });
});
