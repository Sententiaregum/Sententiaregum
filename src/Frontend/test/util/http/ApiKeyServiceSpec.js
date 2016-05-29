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

import ApiKeyService from '../../../util/http/ApiKeyService';
import chai from 'chai';

describe('ApiKeyService', () => {
  beforeEach(() => {
    localStorage.setItem('api_key', Math.random());
  });
  it('checks whether the user is logged in', () => {
    chai.expect(ApiKeyService.isLoggedIn()).to.equal(true);
  });
});
