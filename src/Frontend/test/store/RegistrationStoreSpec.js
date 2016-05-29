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

import RegistrationStore from '../../store/RegistrationStore';
import { CREATE_ACCOUNT, ACCOUNT_VALIDATION_ERROR } from '../../constants/Portal';
import { expect } from 'chai';
import { runAction } from 'sententiaregum-flux-container';

describe('RegistrationStore', () => {
  it('handles success', () => {
    runAction(() => {
      return dispatch => {
        dispatch(CREATE_ACCOUNT, {});
      }
    }, []);

    expect(RegistrationStore.getState()).to.equal(null);
  });

  it('stores validation errors', () => {
    const errors = [
      { username: ['Username already in use!'] },
      { password: ['Password cannot be empty!'] }
    ];

    runAction(() => {
      return dispatch => {
        dispatch(ACCOUNT_VALIDATION_ERROR, {
          errors,
          nameSuggestions: ['Ma27_2016']
        });
      }
    }, []);

    expect(RegistrationStore.getState().errors).to.equal(errors);
    expect(RegistrationStore.getState().suggestions[0]).to.equal('Ma27_2016');
  })
});
