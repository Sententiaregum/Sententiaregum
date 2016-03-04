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
import FormHelper from '../../../util/react/FormHelper';
import counterpart from 'counterpart';
import { stub, spy } from 'sinon';
import mockDOMEventObject from '../../fixtures/mockDOMEventObject';

describe('FormHelper', () => {
  it('builds field alias', () => {
    const helper = new FormHelper({}, {}, {}, () => {}, 'namespace');
    expect(helper.getFormFieldAlias('test')).to.equal('namespace.test');
  });

  it('fetches values from value container', () => {
    localStorage.setItem('namespace.test', 'blah');
    const helper = new FormHelper({}, {}, {}, () => {}, 'namespace');

    expect(helper.getValue(null, 'test')).to.equal('blah');
  });

  it('fetches values from state', () => {
    const helper = new FormHelper({}, {}, {}, () => {}, 'namespace');

    expect(helper.getValue('testval', 'test')).to.equal('testval');
  });

  it('returns null for validation state when form is not submitted', () => {
    const helper = new FormHelper({}, {}, {}, () => {}, 'namespace');
    expect(helper.associateFieldsWithStyle([])).to.equal(null);
  });

  it('returns `success` when a submitted form contains no errors', () => {
    const helper      = new FormHelper({}, {}, {}, () => {}, 'namespace');
    helper._submitted = true;
    expect(helper.associateFieldsWithStyle([])).to.equal('success');
  });

  it('returns `error` when a submitted form contains errors', () => {
    const helper      = new FormHelper({}, {}, {}, () => {}, 'namespace');
    helper._submitted = true;
    expect(helper.associateFieldsWithStyle(['One error', 'And another one'])).to.equal('error');
  });

  it('translates form fields', () => {
    stub(counterpart, 'translate', () => 'Translation');

    const helper = new FormHelper({}, {}, {}, () => {}, 'namespace');
    expect(helper.getTranslatedFormField('test')).to.equal('Translation');
    expect(counterpart.translate.calledOnce).to.equal(true);
    expect(counterpart.translate.calledWith('namespace.test')).to.equal(true);

    counterpart.translate.restore();
  });

  it('creates progress state', () => {
    const helper = new FormHelper({}, {}, {}, () => {}, 'namespace');

    expect(helper.startProgress().progress).to.equal(true);
  });

  it('builds success state', () => {
    const helper = new FormHelper({ username: 'Foo' }, { password: '' }, { extra: {} }, () => {}, 'namespace');

    const nextState = helper.getSuccessState({ username: 'Foo', password: 'Bar' });
    expect(helper.isSubmitted()).to.equal(true);
    expect(nextState.data.password).to.equal('');
    expect(nextState.data.username).to.equal('Foo');
    expect(nextState.success).to.equal(true);
    expect(nextState.progress).to.equal(false);
    expect(Object.keys(nextState.validation.errors).length).to.equal(0);
    expect(Object.keys(nextState.validation.extra).length).to.equal(0);
  });

  it('builds error state', () => {
    const helper = new FormHelper({ username: '' }, {}, {}, () => {}, 'namespace');

    const structure = { property: { en: ['Error'] } };
    const nextState = helper.getErrorState({ username: 'Foo' }, structure, { extra: {} });
    expect(helper.isSubmitted()).to.equal(true);
    expect(nextState.progress).to.equal(false);
    expect(nextState.success).to.equal(false);
    expect(nextState.data.username).to.equal('Foo');
    expect(Object.keys(nextState.data).length).to.equal(1);
    expect(nextState.validation.errors).to.equal(structure);
    expect(typeof nextState.validation.extra).to.equal('object');
  });

  it('causes invariant violation if an insufficient amount of field information is given', () => {
    const helper = new FormHelper({ username: '' }, {}, {}, () => {}, 'namespace');

    expect(() => {
      helper.getErrorState({}, { property: { en: ['Error'] } }, { extra: {} });
    }).to.throw('All form fields must be present in order to avoid fields getting lost as React.JS doesn\'t support deep merging!');
  });

  it('builds the basic initial state', () => {
    const helper = new FormHelper({ username: '' }, {}, {}, () => {}, 'namespace');

    const nextState = helper.getInitialState();
    expect(helper.isSubmitted()).to.equal(false);
    expect(nextState.progress).to.equal(false);
    expect(nextState.success).to.equal(false);

    expect(Object.keys(nextState.data).length).to.equal(1);
    expect(nextState.data.username).to.equal('');

    expect(Object.keys(nextState.validation.errors).length).to.equal(0);
  });

  it('builds state on component remount', () => {
    localStorage.setItem('namespace.username', 'Test');
    const helper = new FormHelper({ username: '' }, {}, {}, () => {}, 'namespace');

    const nextState = helper.getInitialState();
    expect(helper.isSubmitted()).to.equal(false);
    expect(Object.keys(nextState.data).length).to.equal(1);
    expect(nextState.data.username).to.equal('Test');

    localStorage.removeItem('namespace.username');
  });

  it('declares component as submitted if errors exist', () => {
    const helper = new FormHelper({ username: '' }, {}, {}, () => {}, 'namespace');

    const struct    = { property: { en: ['Foolish error'] } };
    const nextState = helper.getInitialState(struct);

    expect(helper.isSubmitted()).to.equal(true);
    expect(nextState.validation.errors).to.equal(struct);
  });

  it('builds a change listener', () => {
    const receiver = spy();
    const helper   = new FormHelper({ username: '' }, {}, {}, receiver, 'namespace');

    const eventObject = mockDOMEventObject({ name: 'username', value: 'Test' });
    const handler     = helper.getChangeListener();

    handler(eventObject);
    expect(receiver.calledOnce).to.equal(true);
    expect(receiver.calledWith({ data: { username: 'Test' } })).to.equal(true);

    expect(localStorage.getItem('namespace.username')).to.equal('Test');

    localStorage.removeItem('namespace.username');
  });

  it('builds a change listener which doesn\'t persist sensitive values', () => {
    const helper = new FormHelper({}, { username: '' }, {}, () => {}, 'namespace');

    const eventObject = mockDOMEventObject({ name: 'username', value: 'Test' });
    const handler     = helper.getChangeListener();

    handler(eventObject);

    expect(localStorage.getItem('namespace.username')).to.equal(null);
  });

  it('checks whether the form is submitted', () => {
    const helper = new FormHelper({}, {}, {}, () => {}, 'namespace');

    expect(helper.isSubmitted()).to.equal(false);

    // simulate change of the submitted clause by telling the helper that the form is submitted
    helper.getSuccessState({});
    expect(helper.isSubmitted()).to.equal(true);
  });

  it('builds error state for single-field validation forms', () => {
    const helper = new FormHelper({}, {}, {}, () => {}, 'namespace', false);
    // simulate change of the submitted clause by telling the helper that the form is submitted
    helper.getErrorState({}, {});

    expect(helper.associateFieldsWithStyle(['blah'])).to.equal('error');
    expect(helper.hasErrors()).to.equal(true);
  });
});
