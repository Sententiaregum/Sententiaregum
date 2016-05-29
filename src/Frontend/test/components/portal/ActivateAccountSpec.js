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

import ActivateAccount from '../../../components/portal/ActivateAccount';
import React from 'react';
import { expect } from 'chai';
import { stub } from 'sinon';
import AccountWebAPIUtils from '../../../util/api/AccountWebAPIUtils';
import { shallow } from 'enzyme';

describe('ActivateAccount', () => {
  it('activates user accounts', () => {
    stub(AccountWebAPIUtils, 'activate', (name, key, success, error) => {
      error();
    });

    const cmp = shallow(<ActivateAccount params={{ name: 'Ma27', key: Math.random() }} />);

    setTimeout(() => {
      expect(cmp.find('DismissableAlertBox').prop('bsStyle')).to.equal('danger');
    });

    AccountWebAPIUtils.activate.restore();
  });

  it('handles activation failures', () => {
    stub(AccountWebAPIUtils, 'activate', (name, key, success) => {
      success();
    });

    const cmp = shallow(<ActivateAccount params={{ name: 'Ma27', key: Math.random() }} />);

    setTimeout(() => {
      expect(cmp.find('DismissableAlertBox').prop('bsStyle')).to.equal('success');
    });

    AccountWebAPIUtils.activate.restore();
  });
});
