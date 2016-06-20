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
import { stub, spy } from 'sinon';
import AccountWebAPIUtils from '../../../../util/api/AccountWebAPIUtils';
import { shallow } from 'enzyme';
import AuthenticationStore from '../../../../store/AuthenticationStore';
import FormHelper from '../../../../util/react/FormHelper';

describe('Form', () => {
  it('handles errors', () => {
    stub(AuthenticationStore, 'getState', () => ({
      message: {
        de: 'Ungültige Zugangsdaten',
        en: 'Invalid credentials'
      }
    }));

    stub(FormHelper.prototype, 'isSubmitted', () => true);
    const cmp = shallow(<Form />);
    cmp.instance()._change();
    cmp.update();

    expect(cmp.find('SimpleErrorAlert').prop('error').en).to.equal('Invalid credentials');

    FormHelper.prototype.isSubmitted.restore();
    AuthenticationStore.getState.restore();
  });

  it('handles success', () => {
    stub(AuthenticationStore, 'getState', () => ({}));

    const replacer = spy();
    const cmp      = shallow(<Form />, {
      context: {
        router: {
          replace: replacer
        }
      }
    });

    cmp.setState({
      data: {
        username: 'Ma27',
        password: '123456'
      }
    });

    cmp.instance()._change();
    expect(replacer.calledOnce).to.equal(true);
    expect(replacer.calledWith('/dashboard')).to.equal(true);

    AuthenticationStore.getState.restore();
  });

  it('handles submit', () => {
    stub(AuthenticationStore, 'getState', () => ({}));
    stub(AccountWebAPIUtils, 'requestApiKey');

    const cmp = shallow(<Form />);
    cmp.setState({
      data: {
        username: 'Ma27',
        password: '123456'
      }
    });

    cmp.simulate('submit', { preventDefault: () => {} });

    expect(AccountWebAPIUtils.requestApiKey.calledOnce);

    expect(AccountWebAPIUtils.requestApiKey.args[0][0]).to.equal('Ma27');
    expect(AccountWebAPIUtils.requestApiKey.args[0][1]).to.equal('123456');

    AccountWebAPIUtils.requestApiKey.restore();
    AuthenticationStore.getState.restore();
  });
});
