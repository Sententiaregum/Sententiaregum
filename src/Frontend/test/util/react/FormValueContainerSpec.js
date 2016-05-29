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
import FormValueContainer from '../../../util/react/FormValueContainer';

describe('FormValueContainer', () => {
  let instance;
  beforeEach(() => {
    instance = new FormValueContainer();
  });

  it('can set/get form values', () => {
    instance.persistFormValue('prefix.value', 'blah');
    expect(instance.getFormValueForAlias('prefix.value')).to.equal('blah');
  });

  it('can purge form values', () => {
    instance.persistFormValue('prefix.value1', 'blah');
    instance.persistFormValue('prefix.value2', 'blah');

    expect(typeof instance.getFormValueForAlias('prefix.value1')).to.not.equal('undefined');
    expect(typeof instance.getFormValueForAlias('prefix.value2')).to.not.equal('undefined');

    instance.purge('prefix');

    setTimeout(() => {
      expect(typeof instance.getFormValueForAlias('prefix.value1')).to.equal('undefined');
      expect(typeof instance.getFormValueForAlias('prefix.value2')).to.equal('undefined');
    }, 100);
  });
});
