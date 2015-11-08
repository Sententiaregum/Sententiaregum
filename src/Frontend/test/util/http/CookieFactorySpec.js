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

import CookieFactory from '../../../util/http/CookieFactory';
import chai from 'chai';
import {jsdom} from 'jsdom';

describe('CookieFactory', () => {
  it('creates cookie object', () => {
    let factory = new CookieFactory(jsdom().parentWindow);
    factory.getCookies().set('test', 'foobar');

    chai.expect(factory.getCookies().get('test')).to.equal('foobar');
  });
});
