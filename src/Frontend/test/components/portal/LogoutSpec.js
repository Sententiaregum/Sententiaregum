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

import { expect } from 'chai';
import { shallow } from 'enzyme';
import Logout from '../../../components/portal/Logout';
import React from 'react';
import { spy, stub } from 'sinon';
import AccountWebAPIUtils from '../../../util/api/AccountWebAPIUtils';

describe('Logout', () => {
  it('renders markup', () => {
    const markup = shallow(<Logout />);
    expect(markup.find('div > h1 > Translate').prop('content')).to.equal('pages.network.logout');

    const progressBar = markup.find('div > ProgressBar');
    expect(progressBar.prop('bsStyle')).to.equal('warning');
    expect(progressBar.prop('now')).to.equal(100);
    expect(progressBar.prop('active')).to.equal(true);
  });

  it('handles redirect', () => {
    const replace = spy();
    const markup  = shallow(<Logout />, {
      context: {
        router: {
          replace
        }
      }
    });

    markup.instance()._redirectAfterLogout();
    expect(replace.calledWith('/')).to.equal(true);
  });
});
