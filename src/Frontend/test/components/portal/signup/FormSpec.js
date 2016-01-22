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

import Form from '../../../../components/portal/signup/Form';
import TestUtils from 'react/lib/ReactTestUtils';
import React from 'react';
import ReactDOM from 'react-dom';
import RegistrationStore from '../../../../store/RegistrationStore';
import sinon from 'sinon';
import chai from 'chai';
import AccountWebAPIUtils from '../../../../util/api/AccountWebAPIUtils';

describe('Form', () => {
  it('handles invalid data and renders its errors into the markup', () => {
    const errors =  {
      username: ['Username in use!']
    };
    const suggestions = ['Ma27_2016'];

    sinon.stub(AccountWebAPIUtils, 'createAccount', (data, success, error) => {
      error({
        name_suggestions: suggestions,
        errors:           errors
      });
    });

    const cmp = TestUtils.renderIntoDocument(<Form />);

    const username = cmp.refs.username;
    const password = cmp.refs.password;
    const email    = cmp.refs.email;

    username.value = 'Ma27';
    TestUtils.Simulate.change(username);

    password.value = '123456';
    TestUtils.Simulate.change(password);

    email.value = 'invalid email';
    TestUtils.Simulate.change(email);

    TestUtils.Simulate.submit(cmp.refs.form);

    const node = ReactDOM.findDOMNode(cmp);

    chai.expect(node._childNodes.length).to.equal(6);
    chai.expect(node._childNodes[1]._childNodes[2]._childNodes[0]._childNodes[0]._childNodes[0]._childNodes[0]._nodeValue).to.equal('Username in use!');
    chai.expect(node._childNodes[0]._childNodes[2]._childNodes[0]._childNodes[0]._nodeValue).to.equal('Ma27_2016');
    chai.expect(node._childNodes[0]._attributes.class._nodeValue).to.equal('alert alert-warning alert-dismissable');

    AccountWebAPIUtils.createAccount.restore();
  });

  it('renders success box after successful account creation', () => {
    sinon.stub(AccountWebAPIUtils, 'createAccount', (data, success, error) => {
      success({});
    });

    const cmp = TestUtils.renderIntoDocument(<Form />);

    const username = cmp.refs.username;
    const password = cmp.refs.password;
    const email    = cmp.refs.email;

    username.value = 'Ma27';
    TestUtils.Simulate.change(username);

    password.value = '123456';
    TestUtils.Simulate.change(password);

    email.value = 'ma27@sententiaregum.dev';
    TestUtils.Simulate.change(email);

    TestUtils.Simulate.submit(cmp.refs.form);

    const node = ReactDOM.findDOMNode(cmp);
    chai.expect(node._childNodes.length).to.equal(7);
    chai.expect(node._childNodes[1]._attributes.class._nodeValue).to.equal('alert alert-success alert-dismissable');

    AccountWebAPIUtils.createAccount.restore();
  });

  afterEach(() => {
    RegistrationStore.constructor(); // run cleanup process
  });
});
