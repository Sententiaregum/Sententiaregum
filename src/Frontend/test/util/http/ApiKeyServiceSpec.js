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
import CookieFactory from '../../../util/http/CookieFactory';
import sinon from 'sinon';
import chai from 'chai';
import jsdom from 'jsdom';

describe('ApiKeyService', () => {
  it('checks whether the user is logged in', () => {
    let window          = jsdom.jsdom().parentWindow;
    let factoryInstance = new CookieFactory(window);
    let mock            = {
      get: () => Math.random()
    };

    sinon.stub(factoryInstance, 'getCookies', () => mock);
    let instance = new ApiKeyService(factoryInstance);

    chai.expect(instance.isLoggedIn()).to.equal(true);

    factoryInstance.getCookies.restore();
  });
});
