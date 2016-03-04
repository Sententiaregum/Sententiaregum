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

import Form from '../../../../components/portal/login/Form';
import React from 'react';
import { expect } from 'chai';
import AccountWebAPIUtils from '../../../../util/api/AccountWebAPIUtils';
import { stub } from 'sinon';
import { shallow } from 'enzyme';

describe('Form', () => {
  it('handles errors', () => {
    stub(AccountWebAPIUtils, 'requestApiKey', (username, login, error) => {
      error({
        message: 'Credentials refused!'
      });
    });

    const cmp = shallow(<Form />);
    cmp.setState({
      data: {
        username: 'Ma27',
        password: '123456'
      }
    });

    cmp.find('form').simulate('submit', { preventDefault: () => {} });

    setTimeout(() => {
      expect(cmp.find('form > DismissableAlertBox > p').contains('Credentials refused!'));
    });

    expect(AccountWebAPIUtils.requestApiKey.calledOnce).to.equal(true);
    AccountWebAPIUtils.requestApiKey.restore();
  });
});
