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

import {jsdom} from 'jsdom';
import HashbangRedirect from '../../../util/http/HashbangRedirect';
import chai from 'chai';

describe('HashbangRedirect', () => {
  it('redirects to other hashbang url', () => {
    const window   = jsdom().parentWindow;
    let redirector = new HashbangRedirect(window);
    redirector.redirect('foo/bar');

    chai.expect(window.location.href).to.equal('file:///#/foo/bar');
  });
});
